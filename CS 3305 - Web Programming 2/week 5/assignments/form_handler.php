<?php
require_once 'Vehicle.php';
require_once 'Car.php';
require_once 'Truck.php';
require_once 'Motorcycle.php';
session_start();

// Get vehicles from session or initialize empty array
$vehicles = $_SESSION['vehicles'] ?? [];
$message = '';
$comparison_result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_vehicle'])) {
        $vehicleType = $_POST['vehicle_type'];
        $brand = trim($_POST['brand']);
        $model = trim($_POST['model']);
        $year = (int)$_POST['year'];
        $price = (float)$_POST['price'];

        try {
            switch ($vehicleType) {
                case 'car':
                    $numberOfDoors = (int)$_POST['number_of_doors'];
                    $vehicle = new Car($brand, $model, $year, $price, $numberOfDoors);
                    break;
                case 'truck':
                    $cargoCapacity = (int)$_POST['cargo_capacity'];
                    $vehicle = new Truck($brand, $model, $year, $price, $cargoCapacity);
                    break;
                case 'motorcycle':
                    $handlebarType = trim($_POST['handlebar_type']);
                    $vehicle = new Motorcycle($brand, $model, $year, $price, $handlebarType);
                    break;
                default:
                    throw new Exception("Invalid vehicle type");
            }
            $vehicles[] = $vehicle;
            $message = "Vehicle added successfully!";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }

    if (isset($_POST['create_sample'])) {
        try {
            $vehicles[] = new Car("Toyota", "Camry", 2023, 25000, 4);
            $vehicles[] = new Truck("Ford", "F-150", 2023, 45000, 1500);
            $vehicles[] = new Motorcycle("Harley-Davidson", "Sportster", 2023, 12000, "Cruiser");
            $message = "Sample vehicles created successfully!";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }

    if (isset($_POST['compare']) && count($vehicles) >= 2) {
        $vehicle1Index = (int)$_POST['vehicle1'];
        $vehicle2Index = (int)$_POST['vehicle2'];
        $criterion = $_POST['comparison_criterion'];
        
        if (isset($vehicles[$vehicle1Index]) && isset($vehicles[$vehicle2Index])) {
            $comparison_result = $vehicles[$vehicle1Index]->compareVehicles($vehicles[$vehicle2Index], $criterion);
        }
    }
}

// Store results in session
$_SESSION['vehicles'] = $vehicles;
$_SESSION['message'] = $message;
$_SESSION['comparison_result'] = $comparison_result;

// Redirect back to form.php
header('Location: form.php');
exit();
?>
