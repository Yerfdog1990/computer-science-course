<?php

require_once 'error-handler.php';

// Validation 1: argument must be provided
if (!array_key_exists(1, $argv)) {
    trigger_error('This script requires a number as first argument', E_USER_ERROR);
    // Script halts here
}

$input = $argv[1];

// Validation 2: input must be numeric
if (!is_numeric($input)) {
    trigger_error(sprintf('A number is expected, got %s', $input), E_USER_ERROR);
    // Script halts here
}

// Validation 3: decimal numbers are rounded with a warning
if (is_float($input * 1)) {
    $input = round($input);
    trigger_error(
        sprintf('Decimal numbers are not allowed. Will use the rounded value [%d]', $input),
        E_USER_WARNING
    );
    // Script continues
}

// Validation 4: negative numbers are converted to absolute value with a warning
if ($input < 0) {
    $input = abs($input);
    trigger_error(
        sprintf('Negative numbers are not allowed. Will use the absolute value [%d].', $input),
        E_USER_WARNING
    );
    // Script continues
}

echo sprintf('sqrt(%d) = ', $input), sqrt((float)$input), PHP_EOL;
