<?php

# Create and initialise an indexed array
$days = array( 'Monday' , 'Tuesday' , 'Wednesday' ) ;

# Display all indexed array elements as a bulleted list
foreach( $days as $value ) { echo "&bull; $value " ; }

# Create and initialise an associative array with keys and values
$months = array( 'jan' => 'January' ,
    'feb' => 'February' , 'mar' => 'March' ) ;

# Display all associative array keys and values as a definition list
echo '<dl>' ;
foreach( $months as $key => $value )
{ echo "<dt>$key<dd>$value" ; }
echo '</dl>' ;

echo '<br>';
echo '<dl>';
foreach( $days as $value )
{ echo "$value <br>"; }
echo '</dl>' ;

