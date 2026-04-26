<?php

// Function with default parameter values
function drink(string $tmp = 'hot', string $flavor = 'tea')
{
    echo "Drinking $tmp $flavor.<br>";
}

// Function calls — using defaults, one override, full override
drink();                    // Drinking hot tea.
drink('iced');              // Drinking iced tea.
drink('cold', 'lemonade'); // Drinking cold lemonade.

// Function using splat operator to accept multiple integer arguments
function add(int ...$numbers)
{
    $total = 0;
    foreach ($numbers as $num) {
        $total += $num; }
    echo "<hr>Total: $total";
}

// Call add() with three integer arguments
add(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);  // Total: 6
add(1, 2, 3);  // Total: 55


