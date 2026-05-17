<?php
// Attempt to open a plain text file for reading, or exit the script upon failure
$file = fopen("manifesto.txt", "r") or die("Unable to open file!");

// Loop through each character until end of file is reached
while (($char = fgetc($file)) !== false) {
    // Display the character
    echo $char;
}

// Close the filestream
fclose($file);
