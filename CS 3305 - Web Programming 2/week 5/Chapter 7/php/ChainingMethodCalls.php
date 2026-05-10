<?php

class ChainingMethodCalls
{

}


// Session class
class Session
{
    public $user;

    public function __construct($hasUser = false)
    {
        $this->user = $hasUser ? new User() : null;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
}

// User class
class User
{
    public function getAddress()
    {
        return new Address();
    }
}

// Address class
class Address
{
    public $country;

    public function __construct()
    {
        $this->country = 'USA';
    }
}

// Test with null user
$session1 = new Session(false);
$country1 = $session1?->user?->getAddress()?->country;
echo 'Session 1 (null user): ' . ($country1 ?? 'NULL') . '<br>';

// Test with actual user
$session2 = new Session(true);
$country2 = $session2?->user?->getAddress()?->country;
echo 'Session 2 (with user): ' . ($country2 ?? 'NULL') . '<br>';

// Test setting user later
$session3 = new Session(false);
$session3->setUser(new User());
$country3 = $session3?->user?->getAddress()?->country;
echo 'Session 3 (user set later): ' . ($country3 ?? 'NULL') . '<br>';
