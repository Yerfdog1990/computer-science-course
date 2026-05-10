<?php

class CreateObject
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

    public function setAge(int $yrs)
    {
        $this->age = $yrs;
    }

    public function setWeight(int $lbs)
    {
        $this->weight = $lbs;
    }

    public function setColor(string $fur)
    {
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

// Create an instance of the Dog class
$fido = new Dog();

// Set the properties using setter methods
$fido->setAge(3);
$fido->setWeight(15);
$fido->setColor('brown');

// Get the properties using getter methods
echo 'Fido is a ' . $fido->getColor() . ' dog<br>';
echo 'Fido is ' . $fido->getAge() . ' years old<br>';
echo 'Fido weighs ' . $fido->getWeight() . ' pounds<br>';

// Call the regular method
$fido->bark();
