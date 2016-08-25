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
}
