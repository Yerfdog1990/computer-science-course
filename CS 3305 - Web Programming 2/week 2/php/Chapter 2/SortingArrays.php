<?php

# Create and initialise an associative array with keys and values
$cars = array( 'Dodge' => 'Viper' ,
    'Chevrolet' => 'Camaro' , 'Ford' => 'Mustang' ) ;

# Display all keys and values in original order
echo '<dl><dt>Original Element Order :<dd>' ;
foreach( $cars as $key => $value )
{ echo ' &bull; ' , $key . ' => ' . $value ; }

# Sort by value (retaining keys) and display
asort( $cars ) ;
echo '<dt>Sorted Into Value Order :<dd>' ;
foreach( $cars as $key => $value )
{ echo ' &bull; ' , $key . ' => ' . $value ; }

# Sort by key and display
ksort( $cars ) ;
echo '<dt>Sorted Into Key Order :<dd>' ;
foreach( $cars as $key => $value )
{ echo ' &bull; ' , $key . ' => ' . $value ; }

echo '</dl>' ;


