<?php
declare(strict_types=1);
require_once 'database.php';

$pdo = Database::getInstance()->getPdo();

$make  = $_POST['make']  ?? '';
$model = $_POST['model'] ?? '';
$year  = $_POST['year']  ?? '';
$color = $_POST['color'] ?? '';
$price = $_POST['price'] ?? '';

// ✅ SAFE — query template with named placeholders
$stmt = $pdo->prepare(
    "INSERT INTO cars (make, model, year, color, price)
     VALUES (:make, :model, :year, :color, :price)"
);

// Bind each placeholder to its value — PDO escapes everything automatically
$stmt->bindParam(':make',  $make,  PDO::PARAM_STR);
$stmt->bindParam(':model', $model, PDO::PARAM_STR);
$stmt->bindParam(':year',  $year,  PDO::PARAM_INT);
$stmt->bindParam(':color', $color, PDO::PARAM_STR);
$stmt->bindParam(':price', $price, PDO::PARAM_STR);

echo "<h2>Placeholder values being bound:</h2>";
echo "<pre>" . htmlspecialchars(print_r([
        ':make'  => $make,
        ':model' => $model,
        ':year'  => $year,
        ':color' => $color,
        ':price' => $price,
    ], true)) . "</pre>";

try {
    $stmt->execute();
    echo "<p style='color:green'>✅ Row inserted safely. ID: " . $pdo->lastInsertId() . "</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
