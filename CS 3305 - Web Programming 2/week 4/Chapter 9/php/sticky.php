<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP Sticky Form</title>
</head>
<body>
<?php
// Initialize variables
$name = '';
$email = '';
$errors = array();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate name field
    if (empty($_POST['name'])) {
        $errors[] = 'name';
    } else {
        $name = trim($_POST['name']);
    }

    // Validate email field
    if (empty($_POST['email'])) {
        $errors[] = 'email';
    } else {
        $email = trim($_POST['email']);
    }

    // Display results
    if (!empty($errors)) {
        echo '<h2 style="color: red;">Error! Please enter your';
        foreach ($errors as $msg) {
            echo " - $msg";
        }
        echo '</h2>';
    } else {
        echo "<h2 style=\"color: green;\">Success! Thanks $name</h2>";
    }
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <p>
        Name:<br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
    </p>
    <p>
        Email:<br>
        <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
    </p>
    <p>
        <input type="submit" value="Submit">
    </p>
</form>
</body>
</html>
