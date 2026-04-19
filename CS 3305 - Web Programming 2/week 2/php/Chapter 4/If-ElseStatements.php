<?php

# Test a single expression — outputs only when TRUE
if ( 4 > 2 )
{
    echo '<p>Yes, 4 is greater than 2 <br>' ;
}
echo '<br>';
# Test two expressions using && — outputs only when BOTH are TRUE
if ( ( 4 > 2 ) && ( 8 > 4 ) )
{
    echo '4 is greater than 2 AND 8 is greater than 4<br>' ;
}
echo '<p>';
# if-else — outputs a result whether the expression is TRUE or FALSE
if ( 4 > 8 )
{
    echo '4 is greater than 8 <br>' ;           // skipped — FALSE
}
else
{
    echo '4 is less than 8 <br>' ;              // executes — FALSE triggers else
}
echo '<p>';
# if-elseif-else — evaluates two expressions with a fallback
if ( 4 > 8 )
{
    echo 'This test is True </p>' ;             // skipped — FALSE
}
elseif ( 8 > 4 )
{
    echo 'Alternative test is True </p>' ;      // executes — TRUE
}
else
{
    echo 'Both tests are False </p>' ;          // skipped — elseif was TRUE
}


