<?php

# Create and initialise three variables
$a = NULL ;
$b = 8 ;
$c = 'PHP Fun' ;

# Output correct grammar based on value of $b
$verb = ( $b == 1 ) ? 'is' : 'are' ;
echo "There $verb $b " ;             // outputs: There are 8

# Display parity of the integer variable $b
$parity = ( $b % 2 != 0 ) ? 'Odd' : 'Even' ;
echo "$b is $parity " ;              // outputs: 8 is Even

# Display the first non-NULL value traversing left to right
$result = $a ?? $b ?? $c ;
echo "abc : $result " ;              // outputs: abc : 8

# Display the first non-NULL value traversing left to right
$result = $c ?? $b ?? $a ;
echo "cba : $result " ;              // outputs: cba : PHP Fun

