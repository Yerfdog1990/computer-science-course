<?php

class InheritingProperties
{

}


// Base class
class Polygon
{
    private float $width;
    private float $height;

    function __construct(float $w = 10, float $h = 5)
    {
        $this->width = $w;
        $this->height = $h;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }
}

// Derived class - Rectangle
class Rectangle extends Polygon
{
    public function area()
    {
        return ($this->getWidth() * $this->getHeight());
    }
}

// Derived class - Triangle
class Triangle extends Polygon
{
    public function area()
    {
        return ($this->getWidth() * $this->getHeight() / 2);
    }
}

// Create instances of derived classes
$rect = new Rectangle();
$trio = new Triangle();

// Output results
echo 'Rectangle Area: ' . $rect->area() . '<br>';
echo 'Triangle Area: ' . $trio->area();

// With custom dimensions
$rect2 = new Rectangle(20, 10);
$trio2 = new Triangle(20, 10);

echo '<br><br>With custom dimensions:<br>';
echo 'Rectangle Area (20x10): ' . $rect2->area() . '<br>';
echo 'Triangle Area (20x10): ' . $trio2->area();
