<?php

// Use 1 — Assign anonymous function to a variable
$hello = function($user) { echo "Hello $user!<br>"; };

// Call the anonymous function via the variable name
$hello('Mike');  // Hello Mike!

// Use 2 — Pass anonymous function as a callback
function greet(callable $anon): void
{
    $anon('Carole Anne');  // calls the passed-in anonymous function
}

greet($hello);  // Hello Carole Anne!

// Use 3 — Return an anonymous function with closure
function meet() : callable
{
    $time = 'morning';
    return function($name) use(&$time)
    {
        return "Good $time, $name!";
    };
}

// Assign the returned anonymous function to a variable
$meeting = meet();

// Call it — accesses $time from the parent scope via closure
echo $meeting('Susan');  // Good morning, Susan!

