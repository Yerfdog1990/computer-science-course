<?php

declare(strict_types=1);
session_start();
require_once '../auth/authorize.php';
require_once '../Database.php';
use app\Database;

requireRole('admin'); // Only admins may delete

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo 'Invalid car ID.';
    exit;
}

$pdo = Database::getInstance()->getPdo();
$stmt = $pdo->prepare("DELETE FROM cars WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: /cars/index.php?deleted=1');
exit;
