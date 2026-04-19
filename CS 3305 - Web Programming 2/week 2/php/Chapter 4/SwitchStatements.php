<?php

# Initialise two variables
$number = 2 ;
$letter = 'B' ;

# Switch statement to match an integer value
switch ( $number )
{
    case 1 : echo 'Number is One<br>' ; break ;
    case 2 : echo 'Number is Two<br>' ; break ;      // matches — executes this
    case 3 : echo 'Number is Three<br>' ; break ;
    default : echo 'Number is Unrecognized<br>' ;
}
echo "<br>";
# Switch statement to match a character value
switch ( $letter )
{
    case 'A' : echo 'Letter is A<br>' ; break ;
    case 'B' : echo 'Letter is B<br>' ; break ;      // matches — executes this
    case 'C' : echo 'Letter is C<br>' ; break ;
    default : echo 'Letter is Unrecognized<br>' ;
}
echo "<br>";
# Switch statement matching multiple values to the same output
switch ( $number )
{
    case 0 : case 1 : case 2 : echo 'Less than 3<br>' ; break ;    // matches 2 — executes
    default : echo '3 or more, or less than zero' ;
}


