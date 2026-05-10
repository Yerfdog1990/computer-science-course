<?php

// Initialize four string variables
$str1 = 'PHP in easy steps';
$str2 = 'PHP in easy steps';
$str3 = 'PHP In Easy Steps';
$str4 = 'admin in easy steps';

// Case-sensitive comparisons using strcmp()
echo "'$str1' versus '$str2' : " . strcmp($str1, $str2) . '<br>';  // 0 — identical
echo "'$str1' versus '$str3' : " . strcmp($str1, $str3) . '<br>';  // 1 — $str1 > $str3 (lowercase > uppercase)
echo "'$str1' versus '$str4' : " . strcmp($str1, $str4) . '<hr>';  // -1 — $str1 < $str4 (uppercase < lowercase)

// Case-insensitive comparison using strcasecmp()
echo 'Comparison Ignoring Case:<br>';
echo "'$str1' versus '$str4' : " . strcasecmp($str1, $str4);       // 0 — equal when ignoring case

// Calculate total ASCII value of $str1 using ord() and strlen()
$total = 0;
for ($i = 0; $i < strlen($str1); $i++)
{
    $total += ord($str1[$i]);
}
echo "<hr>ASCII Total '$str1': $total";
