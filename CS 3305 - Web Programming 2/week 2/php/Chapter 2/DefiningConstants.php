<?php

# Create and initialise a string constant
define( 'USER' , 'Mike' ) ;

# Create and initialise an array constant
define( 'PETS' , [ 'Kitten' , 'Puppy' , 'Hamster' ] ) ;

# Display two constant values in a concatenated string
echo '<p>Hello ' . USER . ' how is your ' . PETS[1] . '?</p>' ;
// outputs: Hello Mike how is your Puppy?

# Display the predefined PHP version constant
echo '<p>You are using PHP version ' . PHP_VERSION ;

# Display the predefined operating system constant
echo ' running on ' . PHP_OS . '</p>' ;


