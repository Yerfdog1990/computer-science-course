<!DOCTYPE html>
<html lang="en">
<head>
    <title>Validation Handler</title>
</head>
<body>
<?php
// Initialize quantity variable with submitted value or NULL
if (!empty($_POST['quantity'])) {
    $quantity = $_POST['quantity'];
} else {
    $quantity = NULL;
    echo 'Quantity field is required<br>';
}

// Ensure the format is numeric
if (!is_numeric($quantity)) {
    $quantity = NULL;
    echo 'Quantity must be numeric<br>';
}

// Initialize email variable with submitted value or NULL
if (!empty($_POST['email'])) {
    $email = $_POST['email'];
    # Format validation to be inserted here
} else {
    $email = NULL;
    echo 'You must enter an email address<br>';
}

// Ensure the email address uses the expected pattern
$pattern = '/\b[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}\b/';
if (!preg_match($pattern, $email)) {
    $email = NULL;
    echo 'Email address is incorrect format<br>';
}

// Output valid submitted data
if (($quantity != NULL) && ($email != NULL)) {
    echo "<h2>Valid Data Received:</h2>";
    echo "Email: $email<br>";
    echo "Quantity: $quantity";
} else {
    echo "<h2>Please correct the errors above</h2>";
}
?>
</body>
</html>
