<?php
/** @var PDO $pdo */
$pdo = require '../config/connection.php';

$insertStmt = "INSERT INTO users (email) VALUES ('john.smith@mail.com')";

if ($pdo->exec($insertStmt) === false) {
    list(, , $driverErrMsg) = $pdo->errorInfo();
    echo "Error inserting: $driverErrMsg" . PHP_EOL;
    return;
}

// Get the auto-generated ID of the newly inserted row
echo "Inserted record with id: " . $pdo->lastInsertId() . PHP_EOL;