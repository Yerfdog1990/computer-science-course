<?php

# Outer for loop — runs 3 iterations
for ( $i = 1 ; $i < 4 ; $i++ )
{
    echo "<p style='color: red;'>Outer loop iteration $i </p>" ;
    # Nested inner for loop — runs 3 iterations on each outer iteration
    for ( $j = 1 ; $j < 4 ; $j++ )
    {
        echo "<p style='color: green;'>Inner loop iteration $j</p>" ;
    }
}


