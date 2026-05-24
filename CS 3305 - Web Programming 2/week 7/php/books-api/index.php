<?php

// Simple router for Abyss Web Server (no .htaccess support)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route /api/books requests to the books.php file
if (preg_match('#^/api/books(/.*)?$#', $uri)) {
    require_once __DIR__ . '/api/books.php';
    exit;
}

// 404 for other routes
http_response_code(404);
echo json_encode(['error' => 'Not found']);
