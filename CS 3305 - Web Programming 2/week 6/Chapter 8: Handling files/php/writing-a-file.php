<?php
// Assign text, including non-printing characters for formatting
$text = "This is the first line.\n";
$text .= "This is the second line with a tab:\tIndented text.\n";
$text .= "This is the third line.\r\n";

// Create a filestream in write mode (creates new file or overwrites existing)
$file = fopen("output.txt", "w") or die("Unable to open file!");

// Write the text to the file
fwrite($file, $text);

// Close the filestream
fclose($file);

echo "File written successfully!";

