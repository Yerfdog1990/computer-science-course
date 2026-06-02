<?php
// connection.http-and-session

$dsn = "mysql:host=127.0.0.1;port=3306;dbname=demo;charset=utf8mb4";

$options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, "http-and-session-user", "http-and-session-pass", $options);

echo sprintf(
    "Connected to MySQL server v%s, on %s",
    $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
    $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
);

return $pdo;


