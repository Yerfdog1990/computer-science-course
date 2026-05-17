<?php
$host = "localhost";
$user = "root";
$pass = "MyNewPassword123!";
$db = "phpecom";

// Creating database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
else {
    echo "Connected successfully";
}

