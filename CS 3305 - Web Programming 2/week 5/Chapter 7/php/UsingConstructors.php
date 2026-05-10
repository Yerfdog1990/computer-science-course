<?php

class UsingConstructors
{

}

class Dog
{
    private $age;
    private $weight;
    private $color;

    function __construct(int $yrs = 2, int $lbs = 8, string $fur = 'black')
    {
        $this->age = $yrs;
        $this->weight = $lbs;
        $this->color = $fur;
    }

    function __destruct()
    {
        echo '<br>Object Destroyed.';
    }

    public function bark()
    {
        echo 'WOOF!';
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
}

// Create objects with constructor
$fido = new Dog(3, 15, 'brown');
$pooch = new Dog(4, 18, 'gray');
$rover = new Dog();

// Display object information
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

// Class interrogation
echo '<br><br>Class Information:<br>';
$items = get_class_vars('Dog');
echo 'Dog variables: ' . count($items) . '<br>';

echo 'Dog methods: ';
$items = get_class_methods('Dog');
foreach ($items as $item) {
    echo "$item, ";
}

// Script ends here - destructors will be called automatically
