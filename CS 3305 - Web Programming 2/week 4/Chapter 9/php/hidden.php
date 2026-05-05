<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP Hidden Form Data</title>
</head>
<body>
<?php
// Define the timezone
date_default_timezone_set('America/New_York');

// Initialize two variables
$user = "Mike";
$time = date("g:i, F j");

// Write a complete form with regular input and hidden fields
echo '<form action="hidden_handler.php" method="POST">';
echo '<h2>Send us your comments</h2>';
echo '<p><textarea name="comment" rows="4" cols="50"></textarea></p>';
echo '<input type="hidden" name="user" value="' . $user . '">';
echo '<input type="hidden" name="time" value="' . $time . '">';
echo '<p><input type="submit" value="Submit"></p>';
echo '</form>';
?>
</body>
</html>