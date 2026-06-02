<?php

declare(strict_types=1);
session_start();
require_once '../auth/authorize.php';
require_once '../Database.php';
use app\Database;

requireRole('admin', 'member'); // Guests may not add cars

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = Database::getInstance()->getPdo();
    $stmt = $pdo->prepare(
        "INSERT INTO cars (make, model, year, color, price)
         VALUES (:make, :model, :year, :color, :price)"
    );
    $stmt->execute([
        ':make' => $_POST['make'] ?? '',
        ':model' => $_POST['model'] ?? '',
        ':year' => (int)($_POST['year'] ?? 0),
        ':color' => $_POST['color'] ?? '',
        ':price' => (float)($_POST['price'] ?? 0),
    ]);
    header('Location: /cars/index.php?added=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Car</title></head>
<body>
<h1>Add a Car</h1>
<form method="post">
    <label>Make: <input type="text" name="make" required></label><br>
    <label>Model: <input type="text" name="model" required></label><br>
    <label>Year: <input type="number" name="year" required></label><br>
    <label>Color: <input type="text" name="color"></label><br>
    <label>Price: <input type="number" name="price" step="0.01"></label><br>
    <button type="submit">Add Car</button>
</form>
</body>
</html>
