<?php
/** @var PDO $pdo */
$pdo = require '../config/connection.php';

$partialMatch = $argv[1] ?? 'john';

$deleteStmt = $pdo->prepare(
    "DELETE FROM users WHERE email LIKE :partialMatch"
);

if ($deleteStmt->execute([':partialMatch' => "%$partialMatch%"]) === false) {
    list(, , $driverErrMsg) = $deleteStmt->errorInfo();
    echo "Error deleting: $driverErrMsg" . PHP_EOL;
    return;
}

$rowCount = $deleteStmt->rowCount();

if ($rowCount) {
    echo sprintf(
            "Deleted %d record(s) matching '%s'.",
            $rowCount,
            $partialMatch
        ) . PHP_EOL;
} else {
    echo sprintf("No records matching '%s' found.", $partialMatch) . PHP_EOL;
}
