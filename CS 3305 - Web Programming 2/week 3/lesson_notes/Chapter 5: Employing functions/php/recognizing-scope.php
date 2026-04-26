<?php

// Initialize global variable
$number = 1;
echo "Global number: $number<br>";  // Global number: 1

// Local scope — same variable name, different value, no conflict
function show_local()
{
    $number = 100;
    echo "Local number: $number<hr>";  // Local number: 100
}
show_local();

// Static variables
function counter1()
{
    static $count = 0;
    $count++;
    echo "Count: $count<br>";
}

echo "<p style='color: green'>Static local variable can be incremented!</p>";
counter1();  // Count: 1
counter1();  // Count: 2
counter1();  // Count: 3


function counter2()
{
    $count = 0;
    $count++;
    echo "Count: $count<br>";
}
echo "<p style='color: red'>Non-static local variable cannot be incremented! </p>";
counter2();  // Count: 1
counter2();  // Count: 1
counter2();  // Count: 1

// Recursive function using global and static variables
function recur()
{
    global $number;
    static $letter = 'A';

    if ($number < 14)
    {
        echo "$number:$letter | ";
        $number++;
        $letter = str_increment($letter);
        recur();
    }
}
recur();

// Display the global variable after recursion has modified it
echo "<hr>Global number: $number";  // Global number: 14



