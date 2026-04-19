<?php

# Create and initialise two variables
$a = 5 ;
$b = 2 ;

# Addition
$result = $a + $b ;
echo "Addition : $result <br><br>" ;         // outputs: 7

# Subtraction
$result = $a - $b ;
echo "Subtraction : $result <br><br>" ;      // outputs: 3

# Multiplication
$result = $a * $b ;
echo "Multiplication : $result <br><br>" ;   // outputs: 10

# Division
$result = $a / $b ;
echo "Division : $result <br><br>" ;         // outputs: 2.5

# Modulus
$result = $a % $b ;
echo "Modulus : $result <br><br>" ;          // outputs: 1

# Exponentiation
$result = $a ** $b ;
echo "Exponentiation : $result <br><br>" ;    // outputs: 25

# floor() - Round Down
# Returns the next lowest integer value by rounding down if necessary.
$result = floor(2.5);
echo "Floor (2.5) : $result <br><br>";    // outputs: 2

$result = floor(2.9);
echo "Floor (2.9) : $result <br><br>";    // outputs: 2

$result = floor(-2.5);
echo "Floor (-2.5) : $result <br><br>";    // outputs: -3

# ceil() - Round Up
# Returns the next highest integer value by rounding up if necessary.
$result = ceil(2.1);
echo "Ceil (2.1) : $result <br><br>";     // outputs: 3

$result = ceil(2.9);
echo "Ceil (2.9) : $result <br><br>";     // outputs: 3

$result = ceil(-2.1);
echo "Ceil (-2.1) : $result <br><br>";    // outputs: -2

# round() - Rounds to the nearest integer (0.5 rounds up)
$result = round(2.5);
echo "Round (2.5) : $result <br><br>";    // outputs: 3

$result = round(2.4);
echo "Round (2.4) : $result <br><br>";    // outputs: 2

# intval() - Converts to integer (truncates decimal part)
$result = intval(2.9);
echo "Intval (2.9) : $result <br><br>";    // outputs: 2

$result = intval(2.1);
echo "Intval (2.1) : $result <br><br>";    // outputs: 2




