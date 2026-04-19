<?php

# Create and initialise two Boolean variables
$yes = TRUE ;
$no  = FALSE ;

# AND evaluations
$result = ( $no  && $no  ) ? 'TRUE' : 'FALSE' ;
echo "No AND No returns $result <br>" ;         // FALSE

$result = ( $yes && $no  ) ? 'TRUE' : 'FALSE' ;
echo "Yes AND No returns : $result <br>" ;      // FALSE

$result = ( $yes && $yes ) ? 'TRUE' : 'FALSE' ;
echo "Yes AND Yes returns $result <hr>" ;       // TRUE

# OR evaluations
$result = ( $no  || $no  ) ? 'TRUE' : 'FALSE' ;
echo "No OR No returns $result <br>" ;          // FALSE

$result = ( $yes || $no  ) ? 'TRUE' : 'FALSE' ;
echo "Yes OR No returns $result <br>" ;         // TRUE

$result = ( $yes || $yes ) ? 'TRUE' : 'FALSE' ;
echo "Yes OR Yes returns $result <hr>" ;        // TRUE

# NOT evaluation
$result = ( ! $yes ) ? 'TRUE' : 'FALSE' ;
echo "NOT Yes returns $result <br>" ;           // FALSE


