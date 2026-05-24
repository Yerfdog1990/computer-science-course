<?php

class JsonMailingListRecipient
{
    public string $email;
    public string $firstName;
    public string $lastName;

    public function __construct(string $email, string $firstName, string $lastName)
    {
        $this->email     = $email;
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }
}
