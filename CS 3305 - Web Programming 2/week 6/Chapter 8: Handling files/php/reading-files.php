<?php
// Attempt to open a plain text file for reading, or exit the script upon failure
$file = fopen("manifesto.txt", "r") or die("Unable to open file!");

// Read the entire file
$content = fread($file, filesize("manifesto.txt"));

// Close the filestream
fclose($file);

// Display the content
echo $content;
