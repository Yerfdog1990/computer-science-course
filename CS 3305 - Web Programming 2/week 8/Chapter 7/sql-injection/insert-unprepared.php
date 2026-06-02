<?php
declare(strict_types=1);
require_once 'database.php';

$pdo = Database::getInstance()->getPdo();

// Read user input directly from POST — no sanitization, no escaping
$make  = $_POST['make']  ?? '';
$model = $_POST['model'] ?? '';
$year  = $_POST['year']  ?? '';
$color = $_POST['color'] ?? '';
$price = $_POST['price'] ?? '';

// ❌ VULNERABLE — user input concatenated directly into the query string
$query = "INSERT INTO cars (make, model, year, color, price)
          VALUES ('$make', '$model', '$year', '$color', '$price')";

echo "<h2>Query being executed:</h2>";
echo "<pre>" . htmlspecialchars($query) . "</pre>";

try {
    $result = $pdo->exec($query);
    echo "<p style='color:green'>✅ Row inserted. Affected rows: $result</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
