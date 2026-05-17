<?php
// Define a custom error handler function to report error level and error message
function error_handler($level, $message) {
    echo "<strong>Error Level:</strong> $level<br>";
    echo "<strong>Error Message:</strong> $message<br><br>";
}

// Nominate the function to handle errors
set_error_handler('error_handler');

// Attempt to access an uninitialized variable
echo($var);

// Try to open a non-existing file
$file = fopen('nonsuch.txt', 'r');

// Explicitly trigger the custom error handler
$number = 2;
if($number >= 1) {
    trigger_error('Value of number must be 1 or less');
}

echo "Script continues after errors are reported.";

# Example: File Operation Error Handling
// Custom error handler for file operations
function file_error_handler($level, $message) {
    echo "<div style='color: red;'>";
    echo "<strong>File Error:</strong> $message<br>";
    echo "<strong>Severity:</strong> $level<br>";
    echo "</div>";
}

// Set the error handler
set_error_handler('file_error_handler');

// Attempt file operations
$file = fopen('data.txt', 'r');
if (!$file) {
    trigger_error('Failed to open data.txt for reading');
}

// Script continues
echo "File operation attempted.";

# Example: User Input Validation
// Custom error handler for validation
function validation_error_handler($level, $message) {
    echo "<div class='error-message'>";
    echo "Validation Error: $message";
    echo "</div>";
}

// Set the error handler
set_error_handler('validation_error_handler');

// Validate user input
$age = 15;
if ($age < 18) {
    trigger_error('User must be 18 or older', E_USER_WARNING);
}

// Script continues
echo "Validation completed.";
