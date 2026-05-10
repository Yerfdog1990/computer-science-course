<?php

require_once 'Vehicle.php';

class Motorcycle extends Vehicle
{
    public string $handlebarType;

    public function __construct($brand, $model, $year, $price, $handlebarType)
    {
        parent::__construct($brand, $model, $year, $price);
        $this->handlebarType = $handlebarType;
    }

    public function displayInfo() : void
    {
        echo "<div class='vehicle-info motorcycle-info'>";
        echo "<h3>Motorcycle Information</h3>";
        echo "<p><strong>Brand:</strong> " . htmlspecialchars($this->brand) . "</p>";
        echo "<p><strong>Model:</strong> " . htmlspecialchars($this->model) . "</p>";
        echo "<p><strong>Year:</strong> " . $this->year . "</p>";
        echo "<p><strong>Price:</strong> $" . number_format($this->price, 2) . "</p>";
        echo "<p><strong>Handlebar Type:</strong> " . htmlspecialchars($this->handlebarType) . "</p>";
        echo "</div>";
    }
}