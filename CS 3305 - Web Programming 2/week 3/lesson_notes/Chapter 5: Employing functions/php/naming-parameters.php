<?php

// Function with default parameter values
function cook(string $prep = 'fried', string $food = 'rice')
{
    echo "<li>Serving $prep $food";
}

// Positional parameter call
cook('boiled', 'egg');                   // Serving boiled egg

// Named parameters — any order
cook(food: 'cheese', prep: 'grilled');  // Serving grilled cheese

// Named parameter — second parameter only, first uses default
cook(food: 'rice');                      // Serving fried rice

// Mix — one positional, one named
cook('baked', food: 'potato');           // Serving baked potato

// Collect named parameters using splat operator
function register(...$args)
{
    foreach ($args as $name => $value)
    {
        echo "<dt>" . $name . "<dd>" . $value;
    }
}

register(Topic: 'PHP', Series: 'Programming');


