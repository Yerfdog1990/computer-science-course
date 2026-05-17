<?php
/** @var PDO $pdo */
$pdo = require '../config/connection.php';

// Apply your PDO options directly to the active connection instance
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// Prepare the SQL query once using your named parameter placeholder
$insertStmt = $pdo->prepare("INSERT INTO users (email) VALUES (:email)");

// The complete array of emails to insert
$emails = [
    'joy.darwin@mail.com',
    'ethan.hunt@mail.com',
    'sarah.connor@mail.com',
    'bruce.wayne@mail.com',
    'clark.kent@mail.com',
    'diana.prince@mail.com',
    'peter.parker@mail.com',
    'tony.stark@mail.com',
    'natasha.romanoff@mail.com',
    'steve.rogers@mail.com',
    'larry.allen@mail.com',
    'arthur.curry@mail.com',
    'hal.jordan@mail.com',
    'selina.kyle@mail.com',
    'harleen.quinzel@mail.com',
    'wade.wilson@mail.com',
    'logan.howlett@mail.com',
    'charles.xavier@mail.com',
    'jean.grey@mail.com',
    'scott.summers@mail.com',
    'smith.munroe@mail.com',
    'ororo.munroe@mail.com'
];

// If a CLI argument is passed, use ONLY that one. Otherwise, use the whole list.
$emailsToProcess = isset($argv[1]) ? [$argv[1]] : $emails;

$successCount = 0;

// Loop through each email in the list and execute the prepared statement
foreach ($emailsToProcess as $email) {
    if ($insertStmt->execute([':email' => $email]) === false) {
        list(, , $driverErrMsg) = $insertStmt->errorInfo();
        echo "Error inserting ($email): $driverErrMsg" . PHP_EOL;
        continue;
    }
    $successCount++;
}

echo "Successfully inserted $successCount record(s)." . PHP_EOL;
