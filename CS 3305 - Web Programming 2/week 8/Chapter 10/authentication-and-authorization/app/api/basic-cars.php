<?php
declare(strict_types=1);

// File is at api/basic-cars.php — Database class is one directory up
require_once '../Database.php';
use app\Database;

/**
 * Parse and validate the HTTP Basic Auth credentials.
 * Sends a 401 challenge and exits if credentials are missing or invalid.
 *
 * @return array  The authenticated user record from the database
 */
function requireBasicAuth(): array
{
    // PHP exposes the Authorization header as HTTP_AUTHORIZATION in $_SERVER
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    // Basic Auth header format: "Basic <base64(email:password)>"
    if (!str_starts_with($authHeader, 'Basic ')) {
        sendBasicAuthChallenge(); // No header at all — prompt the browser
    }

    // Strip the "Basic " prefix and decode the Base64 payload
    $decoded  = base64_decode(substr($authHeader, 6));

    // The decoded string is "email:password" — split on the first colon only
    $colonPos = strpos($decoded, ':');

    if ($colonPos === false) {
        sendBasicAuthChallenge(); // Malformed header
    }

    $email    = substr($decoded, 0, $colonPos);
    $password = substr($decoded, $colonPos + 1);

    // Look up the user by email using a prepared statement
    $pdo  = Database::getInstance()->getPdo();
    $stmt = $pdo->prepare(
        "SELECT id, name, email, password_hash, role
         FROM users
         WHERE email = :email"
    );
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // Verify the submitted password against the stored bcrypt hash
    if (!$user || !password_verify($password, $user['password_hash'])) {
        sendBasicAuthChallenge(); // Wrong credentials — challenge again
    }

    return $user;
}

/**
 * Send a 401 Unauthorized response with the WWW-Authenticate header.
 * The browser will show a native username/password dialog box.
 */
function sendBasicAuthChallenge(): never
{
    // WWW-Authenticate tells the browser to show its built-in login dialog
    header('WWW-Authenticate: Basic realm="Car API"');
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required.']);
    exit;
}

// All responses from this endpoint are JSON
header('Content-Type: application/json');

// Authenticate — exits with 401 if credentials are missing or invalid
$user = requireBasicAuth();

// Authenticated — fetch and return the car list
$pdo  = Database::getInstance()->getPdo();
$cars = $pdo->query("SELECT * FROM cars ORDER BY make")->fetchAll();

echo json_encode([
    'authenticated_as' => $user['name'],
    'role'             => $user['role'],
    'total'            => count($cars),
    'data'             => $cars,
]);