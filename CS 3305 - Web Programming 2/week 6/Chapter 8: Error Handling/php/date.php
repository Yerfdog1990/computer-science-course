<?php

require_once 'all-errors-handler.php';

class Disposable extends Exception {}

function handle(array $input)
{
    if (!isset($input[1])) {
        throw new Disposable('A class name is required (DateTime or DateTimeImmutable).');
    }

    $calleeName = $input[1];

    // Validate: only allow DateTime or DateTimeImmutable
    if (!in_array($calleeName, [DateTime::class, DateTimeImmutable::class])) {
        throw new Disposable('One of DateTime or DateTimeImmutable is expected.');
    }

    $time     = $input[2] ?? 'now';    // Default: current time
    $timezone = $input[3] ?? 'UTC';    // Default: UTC

    // Try building the timezone object; catch any Exception and rethrow as Disposable
    try {
        $dateTimeZone = new DateTimeZone($timezone);
    } catch (Exception $e) {
        throw new Disposable(sprintf('Unknown/Bad timezone: [%s]', $timezone));
    }

    // Try building the DateTime object
    try {
        $dateTime = new $calleeName($time, $dateTimeZone);
    } catch (Exception $e) {
        throw new Disposable(sprintf('Cannot build date from [%s]', $time));
    }

    return $dateTime;
}

try {
    $output = handle($argv);
    echo 'Result: ', print_r($output, true);

} catch (Disposable $e) {
    // Expected, user-facing errors
    echo '(!) ', $e->getMessage(), PHP_EOL;
    exit(1);
    // Any other exception bubbles to the registered exception handler
}
