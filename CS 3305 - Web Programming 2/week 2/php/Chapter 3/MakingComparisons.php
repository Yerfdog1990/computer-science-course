<?php

# Create and initialise five variables
$zero = 0 ;
$zeroString  = "0" ;
$nil  = 0 ;
$one  = 1 ;
$upr  = 'A' ;
$lwr  = 'a' ;

# Equality comparisons
echo "0 == '0' : " ; var_dump( $zero == $zeroString ) ; echo "<br><br>";    // bool(true)
echo "0 === '0' : " ; var_dump( $zero === $zeroString ) ; echo "<br><br>";    // bool(false) -> different types (int vs string)
echo "0 == 1 : " ; var_dump( $zero == $one ) ; echo "<br><br>";    // bool(false)
echo "A == a : " ; var_dump( $upr  == $lwr ) ; echo "<br><br>";    // bool(false)
echo "A != a : " ; var_dump( $upr  != $lwr ) ; echo "<br><br>";    // bool(true)

# Greater than / less than comparisons
echo "1 > 0 : "  ; var_dump( $one  > $nil  ) ; echo "<br><br>";    // bool(true)
echo "0 >= 0 : " ; var_dump( $zero >= $nil ) ; echo "<br><br>";    // bool(true)
echo "1 <= 0 : " ; var_dump( $one  <= $nil ) ; echo "<br><br>";    // bool(false)

# Spaceship operator comparisons
echo "1 <=> 0 : " ; var_dump( $one  <=> $nil  ) ; echo "<br><br>"; // int(1)
echo "1 <=> 1 : " ; var_dump( $one  <=> $one  ) ; echo "<br><br>"; // int(0)
echo "0 <=> 1 : " ; var_dump( $nil  <=> $one  ) ; echo "<br><br>"; // int(-1)


