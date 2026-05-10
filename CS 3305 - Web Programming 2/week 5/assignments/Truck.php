<?php

require_once 'Vehicle.php';

class Truck extends Vehicle
{
    public int $cargoCapacity;

    public function __construct($brand, $model, $year, $price, $cargoCapacity)
    {
        parent::__construct($brand, $model, $year, $price);
        $this->cargoCapacity = $cargoCapacity;
    }

    public function displayInfo() : void
    {
        echo "<div class='vehicle-info truck-info'>";
        echo "<h3>Truck Information</h3>";
        echo "<p><strong>Brand:</strong> " . htmlspecialchars($this->brand) . "</p>";
        echo "<p><strong>Model:</strong> " . htmlspecialchars($this->model) . "</p>";
        echo "<p><strong>Year:</strong> " . $this->year . "</p>";
        echo "<p><strong>Price:</strong> $" . number_format($this->price, 2) . "</p>";
        echo "<p><strong>Cargo Capacity:</strong> " . $this->cargoCapacity . " cubic feet</p>";
        echo "</div>";
    }
}