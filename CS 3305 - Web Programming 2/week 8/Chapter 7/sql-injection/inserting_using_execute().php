<?php
declare(strict_types=1);
require_once 'database.php';

$pdo = Database::getInstance()->getPdo();

// Prepare once — execute many times
$stmt = $pdo->prepare(
    "INSERT INTO cars (make, model, year, color, price)
     VALUES (:make, :model, :year, :color, :price)"
);

$cars = [
    ['make' => 'Toyota',    'model' => 'Camry',      'year' => 2023, 'color' => 'Silver',  'price' => 28000.00],
    ['make' => 'Honda',     'model' => 'Civic',      'year' => 2022, 'color' => 'Blue',    'price' => 24500.00],
    ['make' => 'Ford',      'model' => 'Mustang',    'year' => 2021, 'color' => 'Red',     'price' => 42000.00],
    ['make' => 'BMW',       'model' => '3 Series',   'year' => 2023, 'color' => 'Black',   'price' => 55000.00],
    ['make' => 'Tesla',     'model' => 'Model 3',    'year' => 2023, 'color' => 'White',   'price' => 48000.00],
];

foreach ($cars as $car) {
    $stmt->execute([
        ':make'  => $car['make'],
        ':model' => $car['model'],
        ':year'  => $car['year'],
        ':color' => $car['color'],
        ':price' => $car['price'],
    ]);
    echo "✅ Inserted: {$car['make']} {$car['model']} ({$car['year']})<br>";
}