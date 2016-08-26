<?php

namespace Skippy565\ObjectTranslationBundle\Model;

/**
 * Class AbstractTranslationObject
 * @package Skippy565\Model
 */
abstract class AbstractTranslationObject
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
