<?php

namespace Skippy565\ObjectTranslationBundle\Controller;

/**
 * Class AbstractObjectTranslation
 * @package ObjectTranslationBundle\Controller
 */
abstract class AbstractObjectTranslation
{
    /**
     * @param object $translationObject
     * @return array
     */
    public static function translate($translationObject)
    {
        if (!$translationObject->fromObject) {
            $retArray = self::buildResponse('false', 'No object to translate from', '');

            return $retArray;
        }

        if (!$translationObject->toObject) {
            $retArray = self::buildResponse('false', 'To object not instantiated', '');

            return $retArray;
        }

        $translationProblemArray = [];

        foreach ($translationObject->translationModel as $key => $value) {
            //check against values
            if (!self::checkOverwrite($translationObject, $value)) { //retain the value if set not to overwrite
                self::setValue($translationObject->toObject, $translationObject->toObject, $value, $value); // TODO: ($value, $value) is this the correct argument here? Method signature defines $key, $value
            } else { //overwrite the value
                self::setValue($translationObject->fromObject, $translationObject->toObject, $key, $value);
            }
        }

        if (count($translationProblemArray) == 0) {
            $retArray = self::postProcessMapping($translationObject);
        } else {
            $retArray = self::buildResponse('false', 'Problem with translation', $translationProblemArray);
        }

        return $retArray;
    }

    /**
     * @param object|array $fromObject
     * @param object|array $toObject
     * @param string       $key
     * @param mixed        $value
     * @return array
     * @throws \LogicException
     */
    public static function setValue(&$fromObject, &$toObject, $key, $value)
    {
        $translationProblemArray = [];
        if (is_array($toObject) && is_array($fromObject)) {
            try {
                $toObject[$value] = $fromObject[$key];
            } catch (\Exception $e) {
                $translationProblemArray[count($translationProblemArray)] = 'Problem Translating ' . $key . ' to ' . $value . ' of ' . $e->getMessage();
            }
        } elseif (is_object($toObject) && is_array($fromObject)) {
            try {
                $toObject->__set($value, $fromObject[$key]);
            } catch (\Exception $e) {
                $translationProblemArray[count($translationProblemArray)] = 'Problem Translating ' . $key . ' to ' . $value . ' of ' . $e->getMessage();
            }
        } elseif (is_array($toObject) && is_object($fromObject)) {
            try {
                $toObject[$value] = $fromObject->__get($key);
            } catch (\Exception $e) {
                $translationProblemArray[count($translationProblemArray)] = 'Problem Translating ' . $key . ' to ' . $value . ' of ' . $e->getMessage();
            }
        } elseif (is_object($toObject) && is_object($fromObject)) {
            try {
                $toObject->__set($value, $fromObject->__get($key));
            } catch (\Exception $e) {
                $translationProblemArray[count($translationProblemArray)] = 'Problem Translating ' . $key . ' to ' . $value . ' of ' . $e->getMessage();
            }
        } else {
            $retArray = self::buildResponse('false', 'Unsupported translation types', '');

            return $retArray;
        }

        throw new \LogicException('How did I get here?');
    }

    public static function postProcessMapping($translationObject)
    {
        if (count($translationObject->mappingFunctions) == 0) {
            $retArray = self::buildResponse('true', 'No post processing mapping', $translationObject->toObject);

            return $retArray;
        }

        $translationProblemArray = [];

        foreach ($translationObject->mappingFunctions as $key => $value) {
            //check against values
            if (!self::checkOverwrite($translationObject, $key)) {
                continue;
            }

            if (is_array($translationObject->toObject) && is_array($translationObject->fromObject)) {
                try {
                    $translationObject->toObject[$key] = $translationObject->$value($translationObject->fromObject);
                } catch (\Exception $e) {
                    $translationProblemArray[count($translationProblemArray)] = 'ToObject' . $key . '=' . $value . '(ToObject' . $key . ') of ' . $e->getMessage();
                }
            } elseif (is_object($translationObject->toObject) && is_array($translationObject->fromObject)) {
                try {
                    $translationObject->toObject->__set($key, $translationObject->$value($translationObject->fromObject));
                } catch (\Exception $e) {
                    $translationProblemArray[count($translationProblemArray)] = 'ToObject->__set(' . $key . ', ' . $value . '(FromObject)) of ' . $e->getMessage();
                }
            } elseif (is_array($translationObject->toObject) && is_object($translationObject->fromObject)) {
                try {
                    $translationObject->toObject[$key] = $translationObject->$value($translationObject->fromObject);
                } catch (\Exception $e) {
                    $translationProblemArray[count($translationProblemArray)] = 'ToObject[' . $key . '] = ' . $value . '(FromObject) of ' . $e->getMessage();
                }
            }
        }

        if (count($translationProblemArray) > 0) {
            $retArray = self::buildResponse('false', 'Problem mapping', $translationProblemArray);
        } else {
            $retArray = self::buildResponse('true', 'Mapped', $translationObject->toObject);
        }

        return $retArray;
    }

    //checks overwrite flag + also checks against overwrite values
    public static function checkOverwrite($translationObject, $key)
    {
        //case where always overwrite the value
        if (!isset($translationObject->overwrittingRules[$key])) {
            return true;
        }

        if (is_array($translationObject->toObject)) {
            if (isset($translationObject->overwrittingRules[$key]) && !in_array($translationObject->toObject[$key], $translationObject->overwrittingRules[$key])) {
                return false;
            } else {
                return true;
            }
        } elseif (is_object($translationObject->toObject)) {
            if (isset($translationObject->overwrittingRules[$key])
                && property_exists($translationObject, $key)
                && !in_array($translationObject->toObject->__get($key), $translationObject->overwrittingRules[$key])
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
