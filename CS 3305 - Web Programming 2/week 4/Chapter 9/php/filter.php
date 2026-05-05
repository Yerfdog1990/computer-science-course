<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data Filtering</title>
</head>
<body>
<?php
// Write a styled HTML page heading
$hdr = '<h1 style="color:red">PHP in easy steps</h1>';
echo $hdr;

// Sanitize the page heading by stripping HTML tags
$sanitized_hdr = filter_var($hdr, FILTER_SANITIZE_STRING);
echo "<h3>Sanitized heading: $sanitized_hdr</h3>";

// Function to validate email addresses
function validate($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p><strong>$email IS a valid email address</strong></p>";
    } else {
        echo "<p><strong>$email IS NOT a valid email address</strong></p>";
    }
}

// Test with invalid email containing space
$email = 'mike @example.com';
echo "<h3>Testing invalid email:</h3>";
validate($email);

// Sanitize the email address
$sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
echo "<h3>After sanitization:</h3>";
validate($sanitized_email);

// Additional examples
echo "<h3>Additional filter examples:</h3>";

// Integer validation with range
$age = '25';
$options = array(
    'options' => array(
        'min_range' => 18,
        'max_range' => 120
    )
);

if (filter_var($age, FILTER_VALIDATE_INT, $options)) {
    echo "<p>Age $age is valid (18-120)</p>";
} else {
    echo "<p>Age $age is invalid</p>";
}

// URL validation
$url = 'https://www.example.com';
if (filter_var($url, FILTER_VALIDATE_URL)) {
    echo "<p>URL $url is valid</p>";
} else {
    echo "<p>URL $url is invalid</p>";
}

// Sanitize special characters
$user_input = '<script>alert("XSS")</script>';
$safe_input = filter_var($user_input, FILTER_SANITIZE_SPECIAL_CHARS);
echo "<p>Original: $user_input</p>";
echo "<p>Sanitized: $safe_input</p>";
?>
</body>
</html>
