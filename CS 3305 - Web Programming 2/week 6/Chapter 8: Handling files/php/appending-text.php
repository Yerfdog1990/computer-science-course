<?php
// Assign text, including non-printing characters for formatting
$text = "This is a new line appended to the file.\n";
$text .= "Another appended line with a tab:\tIndented text.\n";
$text .= "Final appended line.\r\n";

// Create a filestream in append mode (preserves existing content)
$file = fopen("output.txt", "a") or die("Unable to open file!");

// Append the text to the file
fwrite($file, $text);

// Close the filestream
fclose($file);

echo "Text appended successfully!";

# Example: Creating a Log File with Timestamps
// Get current date and time
$timestamp = date("Y-m-d H:i:s");

// Create log entry with formatting
$logEntry = "[$timestamp] User accessed the page.\n";
$logEntry .= "[$timestamp] IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
$logEntry .= "[$timestamp] User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
$logEntry .= "----------------------------------------\n";

// Open file in append mode to preserve existing logs
$file = fopen("access.log", "a") or die("Unable to open file!");

// Append the log entry
fwrite($file, $logEntry);

// Close the filestream
fclose($file);

echo "Log entry added!";

# Example: Building a Guest Book
// Get form data (simulated)
$name = "John Doe";
$message = "Great website! Keep up the good work.";
$date = date("Y-m-d H:i:s");

// Create guest book entry
$entry = "========================================\n";
$entry .= "Date: $date\n";
$entry .= "Name: $name\n";
$entry .= "Message: $message\n";
$entry .= "========================================\n\n";

// Open file in append mode
$file = fopen("guestbook.txt", "a") or die("Unable to open file!");

// Append the entry
fwrite($file, $entry);

// Close the filestream
fclose($file);

echo "Thank you for signing our guest book!";

# Example: Sequential Data Collection
// Simulate collecting data
$dataPoint1 = "Temperature: 25°C\n";
$dataPoint2 = "Humidity: 60%\n";
$dataPoint3 = "Pressure: 1013 hPa\n";

// Open file in append mode
$file = fopen("sensor_data.txt", "a") or die("Unable to open file!");

// Append each data point with timestamp
$timestamp = date("Y-m-d H:i:s");
fwrite($file, "[$timestamp] $dataPoint1");
fwrite($file, "[$timestamp] $dataPoint2");
fwrite($file, "[$timestamp] $dataPoint3");
fwrite($file, "\n");

// Close the filestream
fclose($file);

echo "Sensor data recorded!";

