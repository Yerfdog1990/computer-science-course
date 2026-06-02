<?php
declare(strict_types=1);
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
<h1>Dashboard</h1>

<p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</p>

<p>
    <strong>Email:</strong> (not displayed for security)<br>
    <strong>Role:</strong> <?= htmlspecialchars($_SESSION['role']) ?>
</p>

<p><a href="/auth/logout.php">Logout</a></p>

<?php if ($_SESSION['role'] === 'admin'): ?>
    <h2>Admin Actions</h2>
    <ul>
        <li><a href="/cars/create.php">Add Car</a></li>
        <li><a href="/cars/index.php">View Cars</a></li>
    </ul>
<?php endif; ?>

<?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'member'): ?>
    <h2>Member Actions</h2>
    <ul>
        <li><a href="/cars/index.php">View Cars</a></li>
    </ul>
<?php endif; ?>

</body>
</html>
