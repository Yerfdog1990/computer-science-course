<?php

# Create two regular arrays
$letters = array( 'A' , 'B' , 'C' ) ;
$numbers = array( 1 , 2 , 3 ) ;

# Create a two-dimensional array with key names
$matrix = array( 'Letter' => $letters , 'Number' => $numbers ) ;

# Display a single stored value (curly braces required for quoted keys in strings)
echo "<p>Start : {$matrix['Letter'][0]} </p>" ;

# Display all keys and values as two unordered lists using nested foreach
foreach( $matrix as $array => $list )
{
    echo '<ul>' ;
    foreach( $list as $key => $value )
    { echo "<li>$array [ $key ] = $value " ; }
    echo '</ul>' ;
}

