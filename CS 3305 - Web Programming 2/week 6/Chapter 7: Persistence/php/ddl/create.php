<?php
/** @var PDO $pdo */
$pdo = require '../config/connection.php'; // connection.php includes the dbname in DSN

$createStmt = "CREATE TABLE users
(
    id          INT          NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email       VARCHAR(254) NOT NULL UNIQUE,
    signup_time DATETIME     DEFAULT CURRENT_TIMESTAMP NOT NULL
)";

if ($pdo->exec($createStmt) === false) {
    list(, , $driverErrMsg) = $pdo->errorInfo();
    echo "Error creating the users table: $driverErrMsg" . PHP_EOL;
    return;
}

echo "The users table was successfully created." . PHP_EOL;
