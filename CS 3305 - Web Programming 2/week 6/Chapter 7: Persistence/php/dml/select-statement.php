<?php
/** @var PDO $pdo */
$pdo = require '../config/connection.php';

// 1. All records
$result = $pdo->query("SELECT id, email, signup_time FROM users");
echo "All records:" . PHP_EOL;
while ($record = $result->fetch()) {
    echo implode("\t", $record) . PHP_EOL;
}

// 2. Limit to first 2 rows
$result = $pdo->query("SELECT id, email FROM users LIMIT 2");
echo PHP_EOL . "First 2 records:" . PHP_EOL;
while ($record = $result->fetch()) {
    echo implode("\t", $record) . PHP_EOL;
}

// 3. Filter with WHERE
$result = $pdo->query("SELECT id, email FROM users WHERE id > 3");
echo PHP_EOL . "Records where id > 3:" . PHP_EOL;
while ($record = $result->fetch()) {
    echo implode("\t", $record) . PHP_EOL;
}

// 4. Sort with ORDER BY
$result = $pdo->query("SELECT id, email FROM users ORDER BY id DESC");
echo PHP_EOL . "Records ordered by id descending:" . PHP_EOL;
while ($record = $result->fetch()) {
    echo implode("\t", $record) . PHP_EOL;
}
