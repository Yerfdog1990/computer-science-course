<?php

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if ($path === "/api/books" || preg_match("#^/api/books/[0-9]+$#", $path)) {
    require __DIR__ . "/api/books.php";
    return;
}

return false;
