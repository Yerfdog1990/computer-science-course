<?php

// Initialize two variables with the same integer value
$a = $b = 5;

// $val is passed by value, $ref is passed by reference
function modify(int $val, int &$ref)
{
    echo "Passed values: $val, $ref<br>";

    $val++;  // only modifies the local copy
    $ref++;  // modifies the original $b directly

    echo "Incremented values: $val, $ref<hr>";
}

// Call the function
modify($a, $b);

// Display the stored values after the function call
echo "Stored values: $a, $b";


