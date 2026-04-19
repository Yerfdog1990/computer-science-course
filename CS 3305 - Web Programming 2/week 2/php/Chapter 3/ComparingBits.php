<?php

# Create and initialise two integer variables
$x = 5 ;
$y = 10 ;

# Display the original assigned values
echo "X : $x , Y : $y <br><br>" ;    // outputs: X : 5 , Y : 10

# Swap the variable values using three XOR operations (no third variable needed)
$x = $x ^ $y ;
$y = $x ^ $y ;
$x = $x ^ $y ;

# Display the swapped values
echo "X : $x , Y : $y <br><br>" ;    // outputs: X : 10 , Y : 5

