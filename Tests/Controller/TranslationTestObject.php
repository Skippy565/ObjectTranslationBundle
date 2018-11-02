<?php

namespace Skippy565\ObjectTranslationBundle\Tests\Controller;

use Skippy565\ObjectTranslationBundle\Model\AbstractTranslationObject;
use Skippy565\ObjectTranslationBundle\Interfaces\TranslationObjectInterface;

/**
 * Class TranslationTestObject
 * @package Skippy565\ObjectTranslationBundle\Tests\Controller
 */
class TranslationTestObject extends AbstractTranslationObject implements TranslationObjectInterface
{
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
            'email1' => 'noOverride',
            'email2' => 'willOverride',
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
        $this->overwritingRules = [
            'noOverride'    =>  [null],
            'willOverride'  =>  ['Change'],
        ];
    }

    /**
     * Function takes in from object, and returns what value should be set for the key
     *
     * @param mixed $fromObject
     * @return string
     * @throws \Exception
     */
    public function mapName($fromObject)
    {
        if (is_object($fromObject)) {
            return $fromObject->__get('firstName').' '.$fromObject->__get('lastName');
        } elseif (is_array($fromObject)) {
            return $fromObject['firstName'].' '.$fromObject['lastName'];
        }

        throw new \Exception("Neither object or array: ".var_dump($fromObject));
    }
}
