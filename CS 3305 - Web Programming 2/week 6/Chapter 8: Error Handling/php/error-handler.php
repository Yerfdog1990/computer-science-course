<?php

/**
 * Custom error handler
 */
function customErrorHandler(int $errno, string $errstr, string $errfile, int $errline): bool {

    $date = date('Y-m-d H:i:s');

    switch ($errno) {

        case E_USER_ERROR:
            echo "[ERROR] {$date} :: {$errstr}" . PHP_EOL;
            echo "File: {$errfile} on line {$errline}" . PHP_EOL;
            exit(1);

        case E_USER_WARNING:
            echo "[WARNING] {$date} :: {$errstr}" . PHP_EOL;
            break;

        case E_USER_NOTICE:
            echo "[NOTICE] {$date} :: {$errstr}" . PHP_EOL;
            break;

        default:
            echo "[UNKNOWN] {$date} :: {$errstr}" . PHP_EOL;
            break;
    }

    // Return true so PHP does not execute the internal error handler
    return true;
}

/**
 * Register custom handler
 */
set_error_handler('customErrorHandler');
