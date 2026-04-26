<?php

// Initialize the string variable
$str = '| PHP String Fun |';
echo "Original String: $str";

// Reverse the string
echo '<hr>Reversed String: ' . strrev($str);

// Repeat the string 3 times
echo '<hr>Repeated String: ' . str_repeat($str, 3);

// Trim the '|' characters from both ends
echo '<hr>Trimmed String: ' . trim($str, '|');

// Pad the string to 30 characters with '*' added to the left
$pad = str_pad($str, 30, '*', STR_PAD_LEFT);
echo "<hr>Padded String: $pad";

// Split the string around spaces and display each part
echo '<hr>Split String: ';
$token = strtok($str, ' ');

while ($token)
{
    echo "$token … ";
    $token = strtok(' ');
}
