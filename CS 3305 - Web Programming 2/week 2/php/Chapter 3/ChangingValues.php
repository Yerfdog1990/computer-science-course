<?php

# Create and initialise four variables with the same integer value
$a = $b = $c = $d = 5 ;

# Prefix increment and decrement — value changed first, then returned
echo "++A : " . ++$a . " --B : " . --$b . "" ;
// outputs: ++A : 6 --B : 4
echo "<br><br>";
# Postfix increment — current value returned first, then incremented
echo "C++ : " . $c++ . " [now C : " . $c . "]" ;
// outputs: C++ : 5 [now C : 6]
echo "<br><br>";
# Postfix decrement — current value returned first, then decremented
echo "D-- : " . $d-- . " [now D : " . $d . "]" ;
// outputs: D-- : 5 [now D : 4]


