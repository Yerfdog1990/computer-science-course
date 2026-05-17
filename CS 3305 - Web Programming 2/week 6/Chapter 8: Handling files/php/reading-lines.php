<?php
// Attempt to open a plain text file for reading, or exit the script upon failure
$file = fopen("manifesto.txt", "r") or die("Unable to open file!");

// Loop through each line until end of file is reached
while (!feof($file)) {
    // Read one line at a time
    $line = fgets($file);

    // Display the line
    echo $line . "<br>";
}

// Close the filestream
fclose($file);

