<?php

# Create and initialise two variables
$phrase = 'The truth is rarely pure' ;
$author = 'Oscar Wilde' ;

# Display a variable value alone
echo $phrase ;

# Display the variable substituted in a mixed string
echo "<p>It is often said that <q>$phrase</q> </p>" ;

# Concatenate a new string onto the variable
$phrase = $phrase . ' and never simple' ;

# Display both variables substituted in a mixed string
echo "<p><q>$phrase</q><cite>$author</cite></p>" ;

