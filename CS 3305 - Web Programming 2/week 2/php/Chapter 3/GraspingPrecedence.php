<?php
# Precedence in Practice

# Arithmetic Precedence
$result = 2 + 3 * 4 ;
echo "2 + 3 * 4 = $result <br><br>" ;      // 14 — multiplication first

$result = ( 2 + 3 ) * 4 ;
echo "( 2 + 3 ) * 4 = $result <br><br>" ;  // 20 — parentheses override precedence

# Exponentiation is Right-Associative
$result = 2 ** 3 ** 2 ;
echo "2 ** 3 ** 2 = $result <br><br>" ;       // grouped as 2 ** (3 ** 2) = 2 ** 9 = 512

$result = ( 2 ** 3 ) ** 2 ;
echo "( 2 ** 3 ) ** 2 = $result <br><br>" ;   // grouped as (8) ** 2 = 64

# Comparison After Arithmetic
$result = 1 + 5 > 3 + 2 ;
echo "1 + 5 > 3 + 2 = " ;
var_dump( $result ) ;
echo "<br><br>" ;    // (1+5) > (3+2) = 6 > 5 = TRUE
