<?php

namespace Skippy565\ObjectTranslationBundle\Controller;

use Skippy565\ObjectTranslationBundle\Interfaces\TranslationObjectInterface;

/**
 * Class AbstractObjectTranslationController
 * @package Skippy565\ObjectTranslationBundle\Controller
 */
abstract class AbstractObjectTranslationController
{
    private static $translationProblem = [];

    /**
     * @param TranslationObjectInterface $translationObject
     * @return array
     */
    public static function translate(TranslationObjectInterface $translationObject)
    {
        if (!$translationObject->fromObject) {
            $retArray = self::buildResponse('false', 'No object to translate from', '');

            return $retArray;
        }

        if (!$translationObject->toObject) {
            $retArray = self::buildResponse('false', 'To object not instantiated', '');

            return $retArray;
        }

        foreach ($translationObject->translationModel as $key => $value) {
            try {
                //check against values
                if (!self::checkOverwrite($translationObject, $value)) { //retain the value if set not to overwrite
                    self::setValue($translationObject->toObject, $translationObject->toObject, $value, $value);
                } else { //overwrite the value
                    self::setValue($translationObject->fromObject, $translationObject->toObject, $key, $value);
                }
            } catch (\Exception $e) {
                $retArray = self::buildResponse('false', $e->getMessage(), '');

                return $retArray;
            }
        }

        if (count(self::$translationProblem) == 0) {
            $retArray = self::postProcessMapping($translationObject);
        } else {
            $retArray = self::buildResponse('false', 'Problem with translation', self::$translationProblem);
        }

        return $retArray;
    }

    /**
     * @param object|array $fromObject
     * @param object|array $toObject
     * @param string       $key
     * @param mixed        $value
     * @return array
     * @throws \Exception
     */
    public static function setValue(&$fromObject, &$toObject, $key, $value)
    {
        if (is_array($toObject) && is_array($fromObject)) {
            try {
                $toObject[$value] = $fromObject[$key];
            } catch (\Exception $e) {
                self::$translationProblem[count(self::$translationProblem)] = 'Problem Translating '.$key.' to '.$value.' of '.$e->getMessage();
            }
        } elseif (is_object($toObject) && is_array($fromObject)) {
            try {
                $toObject->__set($value, $fromObject[$key]);
            } catch (\Exception $e) {
                self::$translationProblem[count(self::$translationProblem)] = 'Problem Translating '.$key.' to '.$value.' of '.$e->getMessage();
            }
        } elseif (is_array($toObject) && is_object($fromObject)) {
            try {
                $toObject[$value] = $fromObject->__get($key);
            } catch (\Exception $e) {
                self::$translationProblem[count(self::$translationProblem)] = 'Problem Translating '.$key.' to '.$value.' of '.$e->getMessage();
            }
        } elseif (is_object($toObject) && is_object($fromObject)) {
            try {
                $toObject->__set($value, $fromObject->__get($key));
            } catch (\Exception $e) {
                self::$translationProblem[count(self::$translationProblem)] = 'Problem Translating '.$key.' to '.$value.' of '.$e->getMessage();
            }
        } else {
            throw new \Exception('Unsupported translation types');
        }
    }

    public static function postProcessMapping($translationObject)
    {
        if (count($translationObject->mappingFunctions) == 0) {
            $retArray = self::buildResponse('true', 'No post processing mapping', $translationObject->toObject);

            return $retArray;
        }

        foreach ($translationObject->mappingFunctions as $key => $value) {
            //check against values
            if (!self::checkOverwrite($translationObject, $key)) {
                continue;
            }

            if (is_array($translationObject->toObject) && is_array($translationObject->fromObject)) {
                try {
                    $translationObject->toObject[$key] = $translationObject->$value($translationObject->fromObject);
                } catch (\Exception $e) {
                    self::$translationProblem[count(self::$translationProblem)] = 'ToObject'.$key.'='.$value.'(ToObject'.$key.') of '.$e->getMessage();
                }
            } elseif (is_object($translationObject->toObject) && is_array($translationObject->fromObject)) {
                try {
                    $translationObject->toObject->__set($key, $translationObject->$value($translationObject->fromObject));
                } catch (\Exception $e) {
                    self::$translationProblem[count(self::$translationProblem)] = 'ToObject->__set('.$key.', '.$value.'(FromObject)) of '.$e->getMessage();
                }
            } elseif (is_array($translationObject->toObject) && is_object($translationObject->fromObject)) {
                try {
                    $translationObject->toObject[$key] = $translationObject->$value($translationObject->fromObject);
                } catch (\Exception $e) {
                    self::$translationProblem[count(self::$translationProblem)] = 'ToObject['.$key.'] = '.$value.'(FromObject) of '.$e->getMessage();
                }
            } elseif (is_object($translationObject->toObject) && is_object($translationObject->fromObject)) {
                try {
                    $translationObject->toObject->__set($key, $translationObject->$value($translationObject->fromObject));
                } catch (\Exception $e) {
                    self::$translationProblem[count(self::$translationProblem)] = 'ToObject->__set('.$key.', '.$value.'(FromObject) of '.$e->getMessage();
                }
            }
        }

        if (count(self::$translationProblem) > 0) {
            $retArray = self::buildResponse('false', 'Problem mapping', self::$translationProblem);
        } else {
            $retArray = self::buildResponse('true', 'Mapped', $translationObject->toObject);
        }

        return $retArray;
    }

    /**
     * Checks overwrite flag + also checks against overwrite values
     *
     * @param mixed $translationObject
     * @param mixed $key
     * @return bool
     */
    public static function checkOverwrite($translationObject, $key)
    {
        //case where always overwrite the value
        if (!isset($translationObject->overwritingRules[$key])) {
            return true;
        }

        if (is_array($translationObject->toObject)) {
            if (isset($translationObject->overwritingRules[$key]) && !in_array($translationObject->toObject[$key], $translationObject->overwritingRules[$key])) {
                return false;
            } else {
                return true;
            }
        } elseif (is_object($translationObject->toObject)) {
            if (isset($translationObject->overwritingRules[$key])
                && property_exists($translationObject->toObject, $key)
                && !in_array($translationObject->toObject->__get($key), $translationObject->overwritingRules[$key])
            ) {
                return false;
            } else {
                return true;
            }
        }

        //unsupported type
        return true;
    }

    /**
     * @param bool         $status
     * @param string       $message
     * @param object|array $data
     * @return array
     */
    public static function buildResponse($status, $message, $data)
    {
        $retArray = [];
        $tempArray = [];
        $tempArray['status'] = $status;
        $tempArray['message'] = $message;

        $retArray['result'] = $tempArray;
        $retArray['data'] = $data;

        return $retArray;
    }
}
