<?php

namespace Skippy565\ObjectTranslationBundle\Model;

/**
 * Class AbstractTranslationObject
 * @package Skippy565\Model
 */
abstract class AbstractTranslationObject
{
    /**
     * function sets all the defaults and sets the to object to a specified type
     * if the to object isn't sent into the constructor
     *
     * @param FromTestObject $fromObject
     * @param ToTestObject   $toObject
     */
    public function __construct($fromObject, $toObject = null, $clone = false)
    {
        $this->clone = $clone;
        $this->fromObject = $fromObject;
        if (!$toObject) {
            $this->setToObject();
        } else {
            if (!$this->clone) {
                $this->toObject = $toObject;
            } else {
                $this->toObject = is_array($toObject) ? array_replace([], $toObject) : clone $toObject;
            }
        }
        $this->setTranslationModel();
        $this->setMappingFunctions();
        $this->setOverwritingRules();
    }

    /*
     * Boolean to create a clone of the to object to modify
     */
    public $clone;

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
     * The mapping functions. Anything more complicated than
     * a straight key transfer
     */
    public $mappingFunctions;

    /*
     * The overwriting rules
     * sometimes we only want to set a value on under certain circumstances
     */
    public $overwritingRules;
}
