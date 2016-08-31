<?php

namespace Skippy565\ObjectTranslationBundle\Tests\Controller;

/**
 * Class FromTestObject
 * @package Skippy565\ObjectTranslationBundle\Tests\Controller
 */
class FromTestObject
{
    public $firstName;

    public $lastName;

    public $email;

    /**
     * TestObject constructor.
     */
    public function __construct()
    {
        $this->firstName = 'Homer';
        $this->lastName = 'Simpson';
        $this->email = 'homer.simpson@fox.com';
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        throw new \Exception("Undefined property $name.");
    }
}
