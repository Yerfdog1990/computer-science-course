<?php

require 'JsonMailingListRecipient.php';

// Create a new recipient object
$recipient = new JsonMailingListRecipient(
    'jane.smith@example.com',
    'Jane',
    'Smith'
);

// Encode the object as a JSON string
$requestBody = json_encode($recipient);

// Output the JSON string
echo $requestBody . PHP_EOL;


$json = '{"email":"jane.smith@example.com","firstName":"Jane","lastName":"Smith"}';

// Decode to stdClass object
$object = json_decode($json);
echo $object->email . PHP_EOL;       // jane.smith@example.com
echo $object->firstName . PHP_EOL;   // Jane

// Decode to associative array (pass true as second argument)
$array = json_decode($json, true);
echo $array['email'] . PHP_EOL;      // jane.smith@example.com
echo $array['firstName'] . PHP_EOL;  // Jane