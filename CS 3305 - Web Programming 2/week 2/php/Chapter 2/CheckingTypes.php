<?php

# Create a filestream resource and an array containing all data types
$filestream = fopen( 'index.html' , 'r' ) ;

$data = array( 'PHP' , 1 , 2.3 , TRUE , NULL , array() , new Directory , $filestream ) ;

# Display the data type and value stored in each array element
foreach( $data as $type )
{
    var_dump( $type ) ;
    echo '<br>' ;
}

# Destroy the filestream resource
fclose( $filestream ) ;

# Attempt to get the data type of the closed resource
echo gettype( $filestream ) ;    // outputs: resource (closed)


