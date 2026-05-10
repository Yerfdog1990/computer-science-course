<?php

class EmbracingPolymorphism
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

// Interface defining the contract
interface Shape
{
    public function area();
}

// Rectangle class implementing the interface
class Rectangle extends Polygon implements Shape
{
    public function area(): float
    {
        return ($this->getWidth() * $this->getHeight());
    }
}

// Triangle class implementing the interface
class Triangle extends Polygon implements Shape
{
    public function area(): float
    {
        return ($this->getWidth() * $this->getHeight() / 2);
    }
}

// Polymorphic function
function getArea(Shape $shape)
{
    return $shape->area();
}

// Create instances
$rect = new Rectangle(8, 10);
$trio = new Triangle(8, 10);

// Use polymorphic function
echo 'Rectangle Area: ' . getArea($rect) . '<br>';
echo 'Triangle Area: ' . getArea($trio);

// Add more shapes to demonstrate extensibility
class Square extends Polygon implements Shape
{
    public function area(): float
    {
        $side = $this->getWidth();
        return $side * $side;
    }
}

$square = new Square(6, 6);
echo '<br>Square Area: ' . getArea($square);


