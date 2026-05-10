<?php

class Vehicle
{
    public string $brand;
    public string $model;
    public int $year;
    public float $price;
    public static int $count = 0;

    public function __construct($brand, $model, $year, $price)
    {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->price = $price;
        self::$count++;
    }

    public function displayInfo() : void
    {
        echo "<div class='vehicle-info'>";
        echo "<h3>Vehicle Information</h3>";
        echo "<p><strong>Brand:</strong> " . htmlspecialchars($this->brand, ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><strong>Model:</strong> " . htmlspecialchars($this->model, ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><strong>Year:</strong> " . $this->year . "</p>";
        echo "<p><strong>Price:</strong> $" . number_format($this->price, 2) . "</p>";
        echo "</div>";
    }

    public function compareVehicles($otherVehicle, $criterion) : string
    {
        switch ($criterion) {
            case 'price':
                if ($this->price > $otherVehicle->price) {
                    return $this->brand . " " . $this->model . " is more expensive by $" . number_format($this->price - $otherVehicle->price, 2);
                } elseif ($this->price < $otherVehicle->price) {
                    return $this->brand . " " . $this->model . " is cheaper by $" . number_format($otherVehicle->price - $this->price, 2);
                } else {
                    return "Both vehicles have the same price";
                }
                break;
            case 'year':
                if ($this->year > $otherVehicle->year) {
                    return $this->brand . " " . $this->model . " is newer by " . ($this->year - $otherVehicle->year) . " years";
                } elseif ($this->year < $otherVehicle->year) {
                    return $this->brand . " " . $this->model . " is older by " . ($otherVehicle->year - $this->year) . " years";
                } else {
                    return "Both vehicles are from the same year";
                }
                break;
            default:
                return "Invalid comparison criterion. Use 'price' or 'year'.";
        }
    }

    public static function getTotalVehicles() : int
    {
        return self::$count;
    }
}