<?php

require_once 'Vehicle.php';

class Car extends Vehicle
{
    public int $numberOfDoors;

    public function __construct($brand, $model, $year, $price, $numberOfDoors)
    {
        parent::__construct($brand, $model, $year, $price);
        $this->numberOfDoors = $numberOfDoors;
    }

    public function displayInfo() : void
    {
        echo "<div class='vehicle-info car-info'>";
        echo "<h3>Car Information</h3>";
        echo "<p><strong>Brand:</strong> " . htmlspecialchars($this->brand, ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><strong>Model:</strong> " . htmlspecialchars($this->model, ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><strong>Year:</strong> " . $this->year . "</p>";
        echo "<p><strong>Price:</strong> $" . number_format($this->price, 2) . "</p>";
        echo "<p><strong>Number of Doors:</strong> " . $this->numberOfDoors . "</p>";
        echo "</div>";
    }
}