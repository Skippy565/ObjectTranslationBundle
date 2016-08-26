<?php

namespace Skippy565\ObjectTranslationBundle\Tests\Controller;

/**
 * Class ToTestObject
 * @package Skippy565\ObjectTranslationBundle\Tests\Controller
 */
class ToTestObject
{
    public $firstName;

    public $lastName;

    public $email;

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
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

        throw new \Exception("Attempted to read undefined property $name.");
    }
}
