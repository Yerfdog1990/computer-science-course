<?php

class EncapsulatingData
{
}
class Dog {
    private $age;      // Attribute
    private $weight;   // Attribute
    private $color;    // Attribute

    public function bark() {
        echo 'WOOF!';  // Action
    }

    // Plus methods here to store data in the properties
    // Plus methods here to retrieve data from the properties
}

$fido = new Dog();  // Creates an instance named "fido" of the "Dog" class

$fido->bark();  // Outputs WOOF!



