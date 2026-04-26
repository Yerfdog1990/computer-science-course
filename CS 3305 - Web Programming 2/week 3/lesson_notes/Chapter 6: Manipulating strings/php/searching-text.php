<?php

// Initialize string variable
$str = 'Most Users usually find PHP useful.';

// Display string and its length
echo "'$str'<br>String Length: " . strlen($str);

// strpos() — index of first forward match (case-sensitive)
echo "<br>First 'us' found at: " . strpos($str, 'us');   // 14 ('us' in 'usually')

// strrpos() — index of last match found in reverse (case-sensitive)
echo "<br>Final 'us' found at: " . strrpos($str, 'us');  // 28 ('us' in 'useful')

// strstr() — remainder of string from first forward match
echo "<hr>Substring from first 'us': " . strstr($str, 'us');   // 'usually find PHP useful.'

// strrchr() — remainder from last occurrence of a character
echo "<br>Characters from final 'u': " . strrchr($str, 'u');   // 'useful.'

// str_contains() — Boolean check: does 'PHP' appear anywhere?
$result = str_contains($str, 'PHP') ? 'Found' : 'Absent';
echo "<hr>Contains 'PHP': " . $result;  // Found

// str_starts_with() — Boolean check: does string start with 'PHP'?
$result = str_starts_with($str, 'PHP') ? 'True' : 'False';
echo "<br>Starts with 'PHP': " . $result;  // False


