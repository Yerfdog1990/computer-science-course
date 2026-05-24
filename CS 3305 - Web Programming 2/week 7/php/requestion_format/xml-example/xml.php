<?php

require 'XMLMailingListRecipient.php';

// Create a new recipient object
$recipient = new XMLMailingListRecipient(
    'jane.smith@example.com',
    'Jane',
    'Smith'
);

// Encode object to XML
try {
    echo $recipient->toXml() . PHP_EOL;
} catch (DOMException $e) {
    echo $e->getMessage();
}

// Decode XML string
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<recipient>
  <email>jane.smith@example.com</email>
  <firstName>Jane</firstName>
  <lastName>Smith</lastName>
</recipient>';

$data = simplexml_load_string($xml);

echo $data->email . PHP_EOL;       // jane.smith@example.com
echo $data->firstName . PHP_EOL;   // Jane
echo $data->lastName . PHP_EOL;    // Smith