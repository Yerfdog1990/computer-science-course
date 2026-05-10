<?php
require_once 'Vehicle.php';
require_once 'Car.php';
require_once 'Truck.php';
require_once 'Motorcycle.php';
session_start();

// Initialize vehicles array if not exists
if (!isset($_SESSION['vehicles'])) {
    $_SESSION['vehicles'] = [];
}

// Get messages from session
$message = $_SESSION['message'] ?? '';
$comparison_result = $_SESSION['comparison_result'] ?? '';

// Clear messages after displaying
unset($_SESSION['message']);
unset($_SESSION['comparison_result']);

$vehicles = $_SESSION['vehicles'];

// Sync static counter with actual vehicle count from session
Vehicle::$count = count($vehicles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Dealership Vehicle Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .vehicle-info {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .car-info {
            border-left-color: #28a745;
        }
        .truck-info {
            border-left-color: #ffc107;
        }
        .motorcycle-info {
            border-left-color: #dc3545;
        }
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .comparison {
            background-color: #e2e3e5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-weight: bold;
        }
        .stats {
            background-color: #e7f3ff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Car Dealership Vehicle Management System</h1>

    <?php if ($message): ?>
        <div class="message <?php echo str_starts_with($message, 'Error') ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="stats">
        <h3>Total Vehicles in System: <?php echo Vehicle::getTotalVehicles(); ?></h3>
    </div>

    <div class="form-section">
        <h2>Add New Vehicle</h2>
        <form method="post" action="form_handler.php">
            <div class="form-group">
                <label for="vehicle_type">Vehicle Type:</label>
                <select name="vehicle_type" id="vehicle_type" onchange="toggleSpecificFields()">
                    <option value="">Select Vehicle Type</option>
                    <option value="car">Car</option>
                    <option value="truck">Truck</option>
                    <option value="motorcycle">Motorcycle</option>
                </select>
            </div>

            <div class="form-group">
                <label for="brand">Brand:</label>
                <input type="text" name="brand" id="brand" required>
            </div>

            <div class="form-group">
                <label for="model">Model:</label>
                <input type="text" name="model" id="model" required>
            </div>

            <div class="form-group">
                <label for="year">Year:</label>
                <input type="number" name="year" id="year" min="1900" max="2025" required>
            </div>

            <div class="form-group">
                <label for="price">Price ($):</label>
                <input type="number" name="price" id="price" min="0" step="0.01" required>
            </div>

            <div id="car_fields" style="display: none;" class="form-group">
                <label for="number_of_doors">Number of Doors:</label>
                <input type="number" name="number_of_doors" id="number_of_doors" min="2" max="6">
            </div>

            <div id="truck_fields" style="display: none;" class="form-group">
                <label for="cargo_capacity">Cargo Capacity (cubic feet):</label>
                <input type="number" name="cargo_capacity" id="cargo_capacity" min="0">
            </div>

            <div id="motorcycle_fields" style="display: none;" class="form-group">
                <label for="handlebar_type">Handlebar Type:</label>
                <input type="text" name="handlebar_type" id="handlebar_type" placeholder="e.g., Cruiser, Sport, Touring">
            </div>

            <button type="submit" name="add_vehicle">Add Vehicle</button>
        </form>

        <form method="post" action="form_handler.php" style="margin-top: 20px;">
            <button type="submit" name="create_sample">Create Sample Vehicles</button>
        </form>
    </div>

    <?php if ($comparison_result): ?>
        <div class="comparison">
            <h3>Comparison Result:</h3>
            <p><?php echo $comparison_result; ?></p>
        </div>
    <?php endif; ?>

    <?php if (count($vehicles) >= 2): ?>
        <div class="form-section">
            <h2>Compare Vehicles</h2>
            <form method="post" action="form_handler.php">
                <div class="form-group">
                    <label for="vehicle1">Vehicle 1:</label>
                    <select name="vehicle1" id="vehicle1">
                        <?php foreach ($vehicles as $index => $vehicle): ?>
                            <option value="<?php echo $index; ?>">
                                <?php echo $vehicle->brand . ' ' . $vehicle->model; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="vehicle2">Vehicle 2:</label>
                    <select name="vehicle2" id="vehicle2">
                        <?php foreach ($vehicles as $index => $vehicle): ?>
                            <option value="<?php echo $index; ?>">
                                <?php echo $vehicle->brand . ' ' . $vehicle->model; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comparison_criterion">Compare By:</label>
                    <select name="comparison_criterion" id="comparison_criterion">
                        <option value="price">Price</option>
                        <option value="year">Year</option>
                    </select>
                </div>

                <button type="submit" name="compare">Compare Vehicles</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (!empty($vehicles)): ?>
        <div class="form-section">
            <h2>Vehicle Inventory</h2>
            <?php foreach ($vehicles as $vehicle): ?>
                <?php $vehicle->displayInfo(); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleSpecificFields() {
        let vehicleType = document.getElementById('vehicle_type').value;

        document.getElementById('car_fields').style.display = 'none';
        document.getElementById('truck_fields').style.display = 'none';
        document.getElementById('motorcycle_fields').style.display = 'none';

        if (vehicleType === 'car') {
            document.getElementById('car_fields').style.display = 'block';
        } else if (vehicleType === 'truck') {
            document.getElementById('truck_fields').style.display = 'block';
        } else if (vehicleType === 'motorcycle') {
            document.getElementById('motorcycle_fields').style.display = 'block';
        }
    }
</script>
</body>
</html>
