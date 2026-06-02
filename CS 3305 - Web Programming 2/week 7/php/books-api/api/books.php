<?php

require_once __DIR__ . '/../config/database.php';

// ── Response helpers ──────────────────────────────────────────────────────────

/**
 * Send a JSON response with the given status code and exit.
 */
function respond(int $status, mixed $data): never
{
    http_response_code($status);
    header('Content-Type: application/json-example');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Return 400 Bad Request with a message.
 */
function badRequest(string $message): never
{
    respond(400, ['error' => $message]);
}

/**
 * Return 404 Not Found.
 */
function notFound(string $message = 'Resource not found'): never
{
    respond(404, ['error' => $message]);
}

// ── Parse the request ─────────────────────────────────────────────────────────

$method = $_SERVER['REQUEST_METHOD'];

// Extract ID from URI: /api/books or /api/books/5
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts  = explode('/', trim($uri, '/'));
$id     = isset($parts[2]) && is_numeric($parts[2]) ? (int) $parts[2] : null;

// Parse JSON request body for POST and PUT
$body = [];
if (in_array($method, ['POST', 'PUT'])) {
    $raw  = file_get_contents('http-and-session://input');
    $body = json_decode($raw, true) ?? [];
}

// ── database connection ───────────────────────────────────────────────────────
$pdo = Database::getInstance()->getPdo();

// ── Route requests ────────────────────────────────────────────────────────────

match (true) {

    // GET /api/books — retrieve all books
    $method === 'GET' && $id === null => (function () use ($pdo) {
        $stmt = $pdo->query("SELECT * FROM books ORDER BY id ASC");
        respond(200, $stmt->fetchAll());
    })(),

    // GET /api/books/{id} — retrieve one book
    $method === 'GET' && $id !== null => (function () use ($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $book = $stmt->fetch();

        if (!$book) {
            notFound("Book with id $id not found.");
        }

        respond(200, $book);
    })(),

    // POST /api/books — create a new book
    $method === 'POST' => (function () use ($pdo, $body) {
        $required = ['title', 'author', 'genre', 'published', 'isbn'];

        foreach ($required as $field) {
            if (empty($body[$field])) {
                badRequest("Missing required field: '$field'.");
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO books (title, author, genre, published, isbn, available)
            VALUES (:title, :author, :genre, :published, :isbn, :available)
        ");

        $stmt->execute([
            ':title'     => $body['title'],
            ':author'    => $body['author'],
            ':genre'     => $body['genre'],
            ':published' => (int) $body['published'],
            ':isbn'      => $body['isbn'],
            ':available' => isset($body['available']) ? (bool) $body['available'] : true,
        ]);

        $newId = (int) $pdo->lastInsertId();

        // Fetch and return the newly created record
        $fetch = $pdo->prepare("SELECT * FROM books WHERE id = :id");
        $fetch->execute([':id' => $newId]);

        header("Location: /api/books/$newId");
        respond(201, $fetch->fetch());
    })(),

    // PUT /api/books/{id} — replace a book record entirely
    $method === 'PUT' && $id !== null => (function () use ($pdo, $id, $body) {
        // Confirm the book exists
        $check = $pdo->prepare("SELECT id FROM books WHERE id = :id");
        $check->execute([':id' => $id]);
        if (!$check->fetch()) {
            notFound("Book with id $id not found.");
        }

        $required = ['title', 'author', 'genre', 'published', 'isbn'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                badRequest("Missing required field: '$field'.");
            }
        }

        $stmt = $pdo->prepare("
            UPDATE books
            SET title     = :title,
                author    = :author,
                genre     = :genre,
                published = :published,
                isbn      = :isbn,
                available = :available
            WHERE id = :id
        ");

        $stmt->execute([
            ':title'     => $body['title'],
            ':author'    => $body['author'],
            ':genre'     => $body['genre'],
            ':published' => (int) $body['published'],
            ':isbn'      => $body['isbn'],
            ':available' => !isset($body['available']) || (bool)$body['available'],
            ':id'        => $id,
        ]);

        // Return the updated record
        $fetch = $pdo->prepare("SELECT * FROM books WHERE id = :id");
        $fetch->execute([':id' => $id]);
        respond(200, $fetch->fetch());
    })(),

    // DELETE /api/books/{id} — remove a book
    $method === 'DELETE' && $id !== null => (function () use ($pdo, $id) {
        $check = $pdo->prepare("SELECT id FROM books WHERE id = :id");
        $check->execute([':id' => $id]);
        if (!$check->fetch()) {
            notFound("Book with id $id not found.");
        }

        $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute([':id' => $id]);

        respond(204, null);
    })(),

    // No matching route
    default => respond(405, ['error' => 'Method not allowed or invalid endpoint.'])
};
