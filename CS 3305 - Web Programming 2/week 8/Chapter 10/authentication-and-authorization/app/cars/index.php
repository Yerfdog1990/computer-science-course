<?php
declare(strict_types=1);
session_start();
require_once '../auth/authorize.php';
require_once '../Database.php';
use app\Database;

requireLogin();

$pdo  = Database::getInstance()->getPdo();
$cars = $pdo->query("SELECT * FROM cars ORDER BY make, year DESC")->fetchAll();
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Car Inventory</title></head>
<body>
<h1>Car Inventory</h1>
<p>Logged in as <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
    (<?= htmlspecialchars($role) ?>)</p>

<?php if (in_array($role, ['admin', 'member'])): ?>
    <a href="../cars/create.php">+ Add Car</a>
<?php endif; ?>

<table border="1" cellpadding="8">
    <thead>
    <tr>
        <th>ID</th><th>Make</th><th>Model</th><th>Year</th>
        <th>Color</th><th>Price</th>
        <?php if ($role === 'admin'): ?><th>Actions</th><?php endif; ?>
    </tr>
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
            <?php if ($role === 'admin'): ?>
                <td>
                    <form method="post" action="../cars/delete.php"
                          onsubmit="return confirm('Delete this car?')">
                        <input type="hidden" name="id" value="<?= $car['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<a href="/auth/logout.php">Logout</a>
</body>
</html>