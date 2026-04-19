<?php

# Outer for loop — three iterations
for ( $i = 1 ; $i < 4 ; $i++ )
{
    # Inner for loop — three iterations
    for ( $j = 1 ; $j < 4 ; $j++ )
    {
        # continue — skips the first iteration of the inner loop on the first outer pass
        if ( $i == 1 && $j == 1 )
        {
            echo "<p style='color: red;'>Continues inner loop when i = $i and j = $j </p>" ;
            continue ;
        }

        # break — exits the inner loop entirely on the second outer pass
        if ( $i == 2 && $j == 1 )
        {
            echo "<p style='color: blue;>Breaks inner loop when i = $i and j = $j </p>" ;
            break ;
        }

        # Normal output on all other iterations
        echo "<p style='color: purple;>Running i = $i and j = $j </p>" ;
    }
}


