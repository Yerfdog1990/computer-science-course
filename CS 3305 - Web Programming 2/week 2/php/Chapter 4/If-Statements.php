<?php

# Initialise an integer variable
$num = 6 ;

# Test if the number exceeds 5
if ( $num > 5 )
{
    echo "$num exceeds 5" ;        // executes if $num is greater than 5
}
echo "<br><br>";
# Test if the number is 5 or below
if ( $num <= 5 )
{
    echo "$num is below 6" ;       // executes if $num is 5 or less
}
# Test if the number is even
if ( $num % 2 == 0 )
{
    echo "$num is Even" ;          // executes if remainder is 0
}
# Test if the number is odd
if ( $num % 2 != 0 )
{
    echo "$num is Odd" ;           // executes if remainder is not 0
}


