<?php

// Function accepting int or float, returning int or float
function half(int|float $arg) : int|float
{
    echo desc('<dt>Argument', $arg);  // describe the argument
    return $arg / 2;                  // return half the value
}

// Helper function — accepts any mixed type, returns a descriptive string
function desc(string $str, mixed $val) : string
{
    return $str . ' ' . $val . ' - ' . gettype($val);
}

// Call with integer 100
echo desc('<dd>Return', half(100)) . '<hr>';

// Call with integer 33
echo desc('<dd>Return', half(33)) . '<hr>';

// Call with floating-point 3.142
echo desc('<dd>Return', half(3.142));


