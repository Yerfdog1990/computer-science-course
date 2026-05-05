<!DOCTYPE html>
<html lang="en">
<head>
    <title>isset() Handler</title>
</head>
<body>
<?php
// Initialize variable with submitted value or NULL if no value submitted
$definition = isset($_POST['definition']) ? $_POST['definition'] : NULL;

// Output appropriate response according to the variable value
if ($definition != NULL) {
    if ($definition != 'Scripting') {
        echo "<h2>$definition is Incorrect</h2>";
    } else {
        echo "<h2>$definition is Correct</h2>";
    }
} else {
    echo '<h2>You must select one answer</h2>';
}
?>
</body>
</html>
