<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP Form Handler</title>
</head>
<body>
<?php
// Initialize comment variable with submitted value or NULL
$comment = (!isset($_POST['comment'])) ? NULL : $_POST['comment'];

// Initialize variables if hidden form field values have been set
$time = (!isset($_POST['time'])) ? NULL : $_POST['time'];
$user = (!isset($_POST['user'])) ? NULL : $_POST['user'];

// Output valid submitted data
if (($comment != NULL) && ($time != NULL) && ($user != NULL)) {
    echo "<h2>Comment received : \"$comment\"</h2>";
    echo "<p>From $user at $time</p>";
} else {
    echo "<h2>Error: Missing required data</h2>";
}
?>
</body>
</html>
