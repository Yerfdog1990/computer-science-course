<?php

// Display current date, day, time and default timezone (UTC)
echo 'Date: '     . date('jS F Y')             . '<br>';  // e.g. 26th April 2026
echo 'Day: '      . date('l')                  . '<br>';  // e.g. Sunday
echo 'Time: '     . date('h:i:s a')            . '<br>';  // e.g. 10:45:30 am
echo 'Timezone: ' . date_default_timezone_get() . '<hr>'; // UTC

// Change timezone to New York and display updated time
date_default_timezone_set('America/New_York');
echo 'Timezone now: ' . date_default_timezone_get();      // America/New_York
echo '<br>Time now: '  . date('h:i:s a')        . '<hr>'; // time in New York

// Create timestamp for tomorrow and display its formatted date
$d = strtotime('tomorrow');
echo 'Tomorrow: ' . date('l, jS F Y', $d)      . '<br>'; // e.g. Monday, 27th April 2026

// Create timestamp for a specific date and display its month and day
$d = strtotime('July 11, 1994');
echo "David's Birthday: " . date('F jS', $d);             // July 11th


