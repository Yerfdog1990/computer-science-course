<?php
// Define a function that will throw an exception when a conditional test fails
function check_size($size) {
    if ($size <= 10) {
        throw new Exception("Value must exceed 10");
    }
}

// Anticipate a specific error and report the exception
try {
    check_size(5);
} catch (Exception $e) {
    echo 'Size Exception!<br>';
    echo $e->getMessage() . '<br>';
}

// Create a custom exception handler class
class CustomException extends Exception {
    public function get_details() {
        $details = 'File: ' . $this->getFile() . '<br>';
        $details .= 'Line: ' . $this->getLine() . '<br>';
        $details .= $this->getMessage();
        return $details;
    }
}

// Add another function that will throw an exception when a conditional test fails
function check_parity($num) {
    if ($num % 2 !== 0) {
        throw new CustomException("Number: $num<br>Value must be even");
    }
}

// Anticipate a specific error and report the exception
try {
    check_parity(5);
} catch (CustomException $e) {
    echo 'Parity Exception!<br>';
    echo $e->get_details() . '<br>';
}

