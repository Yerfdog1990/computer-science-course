<?php

declare(strict_types=1);
require_once '../Database.php';
use app\Database;

header('Content-Type: application/json');

function authenticateApiKey(): array
{
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;

    if (empty($apiKey)) {
        http_response_code(401);
        echo json_encode(['error' => 'API key missing. Provide X-API-Key header.']);
        exit;
    }

    $pdo = Database::getInstance()->getPdo();
    $stmt = $pdo->prepare(
        "SELECT u.id, u.name, u.role
         FROM api_keys ak
         JOIN users u ON ak.user_id = u.id
         WHERE ak.api_key = :api_key"
    );
    $stmt->execute([':api_key' => $apiKey]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid API key.']);
        exit;
    }

    return $user;
}

$user = authenticateApiKey();
$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::getInstance()->getPdo();

if ($method === 'GET') {
    $cars = $pdo->query("SELECT * FROM cars ORDER BY make")->fetchAll();
    echo json_encode(['data' => $cars, 'requested_by' => $user['name']]);

} elseif ($method === 'POST') {
    if (!in_array($user['role'], ['admin', 'member'], true)) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden. Members and admins only.']);
        exit;
    }

    $body = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare(
        "INSERT INTO cars (make, model, year, color, price)
         VALUES (:make, :model, :year, :color, :price)"
    );
    $stmt->execute([
        ':make' => $body['make'] ?? '',
        ':model' => $body['model'] ?? '',
        ':year' => (int)($body['year'] ?? 0),
        ':color' => $body['color'] ?? '',
        ':price' => (float)($body['price'] ?? 0),
    ]);
    http_response_code(201);
    echo json_encode(['message' => 'Car created.', 'id' => $pdo->lastInsertId()]);

} elseif ($method === 'DELETE') {
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden. Admins only.']);
        exit;
    }
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo json_encode(['message' => "Car $id deleted."]);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
