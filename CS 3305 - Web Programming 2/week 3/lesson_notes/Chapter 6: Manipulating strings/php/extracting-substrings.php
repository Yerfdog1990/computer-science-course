<?php

// Initialize the string variable
$str = 'SQL & PHP: Learning SQL and PHP with SQL examples.';

// Display the string and count occurrences of 'SQL'
echo "'$str'<br>";
echo "SQL Count: " . substr_count($str, 'SQL');   // 3

// Extract from index 27 to the end
echo '<br>Index 27: ' . substr($str, 27);          // 'PHP with SQL examples.'

// Extract 13 characters starting at index 4
echo '<br>Index 4 Length 13: ' . substr($str, 4, 13);  // '& PHP: Learni' (not matching the textbook demo, adjusted below)

// Initialize replacement string
$sub = 'PHP & MySQL';

// Replace the first 3 characters ('SQL') with 'PHP & MySQL'
$str = substr_replace($str, $sub, 0, 3);
echo "<br>'$str'";  // 'PHP & MySQL & PHP: Learning SQL and PHP with SQL examples.'


