<?php

class InitializingMembers
{
}


class Dog
{
    private $age;
    private $weight;
    private $color;

    public function bark()
    {
        echo 'WOOF!';
    }

    public function setValues(int $yrs = 2, int $lbs = 8, string $fur = 'black')
    {
        $this->age = $yrs;
        $this->weight = $lbs;
        $this->color = $fur;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getColor()
    {
        return $this->color;
    }
}

// Create and configure first object
$fido = new Dog();
$fido->setValues(3, 15, 'brown');

// Create and configure second object
$pooch = new Dog();
$pooch->setValues(4, 18, 'gray');

// Create and configure third object with defaults
$rover = new Dog();
$rover->setValues(); // Uses default values: age=2, weight=8, color='black'

// Display all objects
echo 'Fido is a ' . $fido->getColor() . ' dog<br>';
echo 'Fido is ' . $fido->getAge() . ' years old<br>';
echo 'Fido weighs ' . $fido->getWeight() . ' pounds<br>';
$fido->bark();

echo '<br>Pooch: ' . $pooch->getAge() . 'yrs ';
echo $pooch->getWeight() . 'lbs ' . $pooch->getColor() . '<br>';
$pooch->bark();

echo '<br>Rover: ' . $rover->getAge() . 'yrs ';
echo $rover->getWeight() . 'lbs ' . $rover->getColor() . '<br>';
$rover->bark();




