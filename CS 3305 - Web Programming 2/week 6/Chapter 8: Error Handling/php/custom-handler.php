<?php

// Define the custom error handler
$errorHandler = function (int $code, string $message, string $file, int $line) {
    echo date(DATE_W3C), " :: $message, in [$file] on line [$line] (error code $code)", PHP_EOL;
};

// Register it for all error types
set_error_handler($errorHandler, E_ALL);

// Trigger errors: $width and $height are undefined
echo $width / $height, PHP_EOL;
