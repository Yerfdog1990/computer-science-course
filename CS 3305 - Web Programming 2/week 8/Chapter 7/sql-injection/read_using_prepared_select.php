<?php
declare(strict_types=1);
require_once 'database.php';

$pdo = Database::getInstance()->getPdo();

// User wants to filter by make — comes from a GET parameter
$filterMake = $_GET['make'] ?? '';

if (!empty($filterMake)) {
    // ✅ Safe — user input goes through a placeholder
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE make = :make ORDER BY year DESC");
    $stmt->execute([':make' => $filterMake]);
} else {
    // No filter — fetch all
    $stmt = $pdo->query("SELECT * FROM cars ORDER BY make, year DESC");
}

$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Car Inventory</title></head>
<body>
<h1>Car Inventory</h1>

<form method="get">
    <label>Filter by Make:
        <input type="text" name="make" value="<?= htmlspecialchars($filterMake) ?>">
    </label>
    <button type="submit">Filter</button>
    <a href="?">Show All</a>
</form>

<table border="1" cellpadding="8">
    <thead>
    <tr><th>ID</th><th>Make</th><th>Model</th><th>Year</th><th>Color</th><th>Price</th></tr>
    </thead>
    <tbody>
    <?php foreach ($cars as $car): ?>
        <tr>
            <td><?= $car['id'] ?></td>
            <td><?= htmlspecialchars($car['make']) ?></td>
            <td><?= htmlspecialchars($car['model']) ?></td>
            <td><?= $car['year'] ?></td>
            <td><?= htmlspecialchars($car['color']) ?></td>
            <td>$<?= number_format((float)$car['price'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
