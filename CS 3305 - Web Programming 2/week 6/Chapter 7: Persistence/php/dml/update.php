<?php
/** @var PDO $pdo */
$pdo = require '../config/connection.php';

// Force native prepared statements to use strict data types
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// 1. Cast the ID to an integer to ensure safe matching
$updateId    = isset($argv[1]) ? (int)$argv[1] : 1;
$updateEmail = $argv[2] ?? 'johnnah.doe@mail.com';

// 2. Validate that an email was actually passed before running the query
if (empty($updateEmail)) {
    echo "Error: Missing email argument. Usage: php update.php <id> <email>" . PHP_EOL;
    return;
}

$updateStmt = $pdo->prepare(
    "UPDATE users SET email = :email WHERE id = :id"
);

// 3. Bind parameters directly into the array structure
$params = [
    ':id'    => $updateId,
    ':email' => $updateEmail
];

if ($updateStmt->execute($params) === false) {
    list(, , $driverErrMsg) = $updateStmt->errorInfo();
    echo "Error: $driverErrMsg" . PHP_EOL;
    return;
}

echo sprintf(
        "Query successful. %d row(s) affected.",
        $updateStmt->rowCount()
    ) . PHP_EOL;
