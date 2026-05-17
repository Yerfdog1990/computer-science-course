<?php

require_once 'all-errors-handler.php';

// Custom exception for anticipated, user-visible errors
class Disposable extends Exception {}

function handle(array $input)
{
    // Throw if no function/class name provided
    if (!isset($input[1])) {
        throw new Disposable('A function/class name is required as the first argument.');
    }

    $calleeName      = $input[1];
    $calleeArguments = array_slice($input, 2); // Arguments start at index 2

    if (function_exists($calleeName)) {
        return call_user_func_array($calleeName, $calleeArguments);

    } elseif (class_exists($calleeName)) {
        return new $calleeName(...$calleeArguments);

    } else {
        throw new Disposable(sprintf('The [%s] function or class does not exist.', $calleeName));
    }
}

try {
    $output = handle($argv);
    // Print result — use var_export() if output is falsy to show the actual type
    echo 'Result: ', $output ? print_r($output, true) : var_export($output, true), PHP_EOL;

} catch (Disposable $e) {
    // Only catch our anticipated errors; let everything else reach the exception handler
    echo '(!) ', $e->getMessage(), PHP_EOL;
    exit(1);
}