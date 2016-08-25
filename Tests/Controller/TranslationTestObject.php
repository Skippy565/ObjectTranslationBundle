<?php

namespace Skippy565\ObjectTranslationBundle\Tests\Controller;

use Skippy565\ObjectTranslationBundle\Interfaces\TranslationObjectInterface;

/**
 * Class TranslationTestObject
 * @package Skippy565\ObjectTranslationBundle\Tests\Controller
 */
class TranslationTestObject implements TranslationObjectInterface
{
    /*
    * The object /translate to start with (Array / Object)
    */
    public $fromObject;

    /*
    * The object / structure to translate to (Array / Object)
    */
    public $toObject;

    /*
    * The translation model for the to / from rules
    */
    public $translationModel;

    /*
    * The mapping functions.  Anything more complicated than
    * a straight key transfer
    */
    public $mappingFunctions;

    /*
    * The overwriting rules
    * sometimes we only want to set a value on under certain circumstances
    */
    public $overwritingRules;

    /**
     * function sets all the defaults and sets the to object to a specified type
     * if the to object isn't sent into the constructor
     *
     * @param FromTestObject $fromObject
     * @param ToTestObject   $toObject
     */
    public function __construct($fromObject, $toObject = null)
    {
        $this->fromObject = $fromObject;
        if (!$toObject) {
            $this->setToObject();
        } else {
            $this->toObject = $toObject;
        }
        $this->setTranslationModel();
        $this->setMappingFunctions();
        $this->setOverwritingRules();
    }

    /**
     * translating outward is a custom model / hash
     * return the data structure
     */
    public function setToObject()
    {
        $this->toObject = new ToTestObject();
    }

    /**
     * key value rules of straight translation
     * key is the from object property / key
     * value is the to object property / key
     */
    public function setTranslationModel()
    {
        $this->translationModel = [
            'email' => 'email',
        ];
    }

    /**
     * hash of translation functions to call
     * key is the value to set
     * value is the function to call
     * using the from Object
     */
    public function setMappingFunctions()
    {
        $this->mappingFunctions = [
            'fullName' => 'mapName',
        ];
    }

    /**
     * has key to overwrite, and an array of values good to overwrite.
     * if it is always overwritten, take care of that in mapping functions.
     * if it is never to be overwritten, have an empty array
     *
     * @return array
     */
    public function setOverwritingRules()
    {
        return [];
    }

    /**
     * function takes in from object, and returns what value should be set for the key
     *
     * @param FromTestObject $fromObject
     * @return string
     */
    public function mapName(FromTestObject $fromObject)
    {
        return $fromObject->firstName . ' ' . $fromObject->lastName;
    }
}
