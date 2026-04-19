<?php

# Initialise two variables
$number = 2 ;
$letter = 'B' ;

# Match statement to match an integer value
echo match ( $number )
{
    1       => 'Number is One' ,
    2       => 'Number is Two' ,
    3       => 'Number is Three' ,
    default => 'Number is Unrecognized' ,
} ;
echo "<br>";
# Match statement to match a character value
echo match ( $letter )
{
    'A'     => 'Letter is A' ,
    'B'     => 'Letter is B' ,
    'C'     => 'Letter is C' ,
    default => 'Letter is Unrecognized' ,
} ;
echo "<br>";
# Match statement matching multiple values to the same return value
echo match ( $number )
{
    0, 1, 2 => 'Less than 3' ,
    default => '3 or more, or less than zero' ,
} ;

