<?php

namespace ObjectTranslationBundle\Interface;

interface ObjectTranslation
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
    public $translationModel

    /*
    * The mapping functions.  Anything more complicated than
    * a straight key transfer
    */
    public $mappingFunctions;

    /*
    * The overwritting rules
    * sometimes we only want to set a value on under certain circumstances
    */
    public $overwrittingRules;

    /*
    * funtion sets all the defaults and sets the to object to a specified type
    * if the to object isn't sent into the constructor
    */
    public function __construct($fromObject, $toObject = null);

    /*
    * translating outward is a custom model / hash
    * return the data structure
    */
    private function setToObject();

    /*
    * key valuerules of straight translation
    * key is the from object property / key
    * value is the to object property / key
    */
    private function setTranslationModel();

    /*
    * hash of translation functions to call
    * key is the value to set
    * value is the function to call
    * using the from Object
    */
    private function setMappingFunctions();

    //has key to overwrite, and an array of values good to overwrite.
    //if it is always overwritten, take care of that in mapping functions.
    //if it is never to be overwritten, have an empty array
    private function setOverwrittingRules();
}