<?php

class XMLMailingListRecipient
{
    public string $email;
    public string $firstName;
    public string $lastName;

    public function __construct(string $email, string $firstName, string $lastName)
    {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * Convert object to XML
     * @throws DOMException
     */
    public function toXml(): string
    {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $recipient = $xml->createElement('recipient');
        $xml->appendChild($recipient);

        $recipient->appendChild(
            $xml->createElement('email', $this->email)
        );

        $recipient->appendChild(
            $xml->createElement('firstName', $this->firstName)
        );

        $recipient->appendChild(
            $xml->createElement('lastName', $this->lastName)
        );

        return $xml->saveXML();
    }
}