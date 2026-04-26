<?php

// Basic function
function greet()
{
    echo 'Hello User!<br>';
}

// Function with typed parameters
function cube(int $n)
{
    echo $n ** 3 . '<br>';
}

// Nested functions
function outer()
{
    function inner()
    {
        echo 'Inner function called.<br>';
    }
    echo 'Inner function created.<br>';
}

// Calling the functions
greet();   // Hello User!
cube(3);   // 27
outer();   // Inner function created.
inner();   // Inner function called.

