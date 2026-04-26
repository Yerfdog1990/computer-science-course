<?php

// Function declared to return only an array
function supply() : array
{
    return array(75, 3.142, 'Super PHP', True);
}

// Call the function and store the returned array
$array = supply();

// Loop through and display each value
foreach ($array as $data)
{
    echo "Element Value: $data<br>";
}

