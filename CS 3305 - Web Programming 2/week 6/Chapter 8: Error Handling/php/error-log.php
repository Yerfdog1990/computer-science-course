<?php

$errorHandler = function (int $code, string $message, string $file, int $line) {
    static $stream; // Persists across multiple calls within the same script run

    if (is_null($stream)) {
        $stream = fopen(__DIR__ . 'week 6/Chapter 8: Error Handling/php/app.log', 'a'); // Open once, append mode
    }

    fwrite(
        $stream,
        date(DATE_W3C) . " :: $message, in [$file] on line [$line] (error code $code)" . PHP_EOL
    );
};

set_error_handler($errorHandler, E_ALL);

//$width = 10;
//$height = 0;

echo $width / $height, PHP_EOL;
