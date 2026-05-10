<?php

class PromotingProperties
{

}

// Traditional approach
class Paint_1
{
    private string $color;

    function __construct(string $hue = 'Blue')
    {
        $this->color = $hue;
    }

    public function setColor(string $c)
    {
        $this->color = $c;
    }

    public function getColor()
    {
        return $this->color;
    }
}

// Promoted properties approach
class Paint_2
{
    function __construct(private string $color = 'Red')
    {
    }

    public function setColor(string $c)
    {
        $this->color = $c;
    }

    public function getColor()
    {
        return $this->color;
    }
}

// Test traditional approach
echo 'Traditional Approach:<br>';
$pot_1 = new Paint_1();
echo $pot_1->getColor() . '<br>';
echo $pot_1->setColor('Yellow');
echo $pot_1->getColor() . '<br>';

// Test promoted properties
echo '<br>Promoted Properties:<br>';
$pot_2 = new Paint_2();
echo $pot_2->getColor() . '<br>';
echo $pot_2->setColor('Green');
echo $pot_2->getColor() . '<br>';
