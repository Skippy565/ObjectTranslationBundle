<?php

namespace ObjectTranslationBundle\Controller;

abstract class Util_Service_ObjectTranslation
{
	public static function Translate($translationObject)
	{
		if (!$translationObject->fromObject)
		{
			$retArray = self::buildResponse('false', 'No object to translate from', '');
			return $retArray;
		}

		if (!$translationObject->toObject)
		{
			$retArray = self::buildResponse('false', 'To object not instantiated', '');
			return $retArray;
		}

		$TranslationProblemArray = [];

		foreach ($translationObject->translationModel as $Key=>$Value)
		{
			//check against values
			if (!self::checkOverwrite($translationObject, $Value)) { //retain the value if set not to overwrite
				self::setValue($translationObject->toObject, $translationObject->toObject, $Value, $Value);
			} else { //overwrite the value
				self::setValue($translationObject->fromObject, $translationObject->toObject, $Key, $Value);
			}
		}

		if (count($TranslationProblemArray) == 0)
		{
			$retArray = self::PostProcessMapping($translationObject);
		}
		else
		{
			$retArray = self::buildResponse('false', 'Problem with translation', $TranslationProblemArray);
		}
		return $retArray;
	}

	public static function setValue(&$fromObject, &$toObject, $Key, $Value)
	{
		$TranslationProblemArray = [];
		if (is_array($toObject) && is_array($fromObject))
		{
			try
			{
				$toObject[$Value]=$fromObject[$Key];
			}
			catch (Exception $e)
			{
				$TranslationProblemArray[count($TranslationProblemArray)] = 'Problem Translating '.$Key.' to '.$Value . ' of ' . $e->getMessage();
			}
		}
		else
		if (is_object($toObject) && is_array($fromObject))
		{
			try 
			{
				$toObject->__set($Value, $fromObject[$Key]);
			}
			catch (Exception $e)
			{
				$TranslationProblemArray[count($TranslationProblemArray)] = 'Problem Translating ' . $Key . ' to ' . $Value . ' of ' . $e->getMessage();
			}
		}
		else
		if (is_array($toObject) && is_object($fromObject))
		{
			try 
			{
				$toObject[$Value] = $fromObject->__get($Key);
			}
			catch (Exception $e)
			{
				$TranslationProblemArray[count($TranslationProblemArray)] = 'Problem Translating ' . $Key . ' to ' . $Value . ' of ' . $e->getMessage();
			}
		}
		else
		if (is_object($toObject) && is_object($fromObject))
		{
			try 
			{
				$toObject->__set($Value, $fromObject->__get($Key));
			}
			catch (Exception $e)
			{
				$TranslationProblemArray[count($TranslationProblemArray)] = 'Problem Translating ' . $Key . ' to ' . $Value . ' of ' . $e->getMessage();
			}			
		}
		else
		{
			$retArray = self::buildResponse('false', 'Unsupported translation types', '');
			return $retArray;
		}
	}

	public static function PostProcessMapping($translationObject)
	{
		if (count($translationObject->mappingFunctions) == 0)
		{
			$retArray = self::buildResponse('true', 'No post processing mapping', $translationObject->toObject);
			return $retArray;
		}

		$TranslationProblemArray = array();

		foreach ($translationObject->mappingFunctions as $key=>$value)
		{
			//check against values
			if (!self::checkOverwrite($translationObject, $key))
			{
				continue;
			}

			if (is_array($translationObject->toObject) && is_array($translationObject->fromObject))
			{
				try
				{
					$translationObject->toObject[$key] = $translationObject->$value($translationObject->fromObject);
				}
				catch (Exception $e)
				{
					$TranslationProblemArray[count($TranslationProblemArray)] = 'ToObject'.$key.'='.$value.'(ToObject'.$key.') of ' . $e->getMessage();
				}
			}
			else
			if (is_object($translationObject->toObject) && is_array($translationObject->fromObject))
			{
				try
				{
					$translationObject->toObject->__set($key, $translationObject->$value($translationObject->fromObject));
				}
				catch (Exception $e)
				{
					$TranslationProblemArray[count($TranslationProblemArray)] = 'ToObject->__set('.$key.', '.$value.'(FromObject)) of ' . $e->getMessage();
				}
			}
			else
			if (is_array($translationObject->toObject) && is_object($translationObject->fromObject))
			{
				try
				{
					$translationObject->toObject[$key] = $translationObject->$value($translationObject->fromObject);
				}
				catch (Exception $e)
				{
					$TranslationProblemArray[count($TranslationProblemArray)] = 'ToObject['.$key.'] = '.$value.'(FromObject) of ' . $e->getMessage();
				}
			}
		}

		if (count($TranslationProblemArray) > 0)
		{
			$retArray = self::buildResponse('false', 'Problem mapping', $TranslationProblemArray);
		}
		else 
		{
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

		if (is_array($translationObject->toObject))
		{
			if (isset($translationObject->overwrittingRules[$key]) && !in_array($translationObject->toObject[$key], $translationObject->overwrittingRules[$key]))
				return false;
			else
				return true;
		}
		else
		if (is_object($translationObject->toObject))
		{
			if (isset($translationObject->overwrittingRules[$key]) 
				&& property_exists($translationObject, $key)
				&& !in_array($translationObject->toObject->__get($key), $translationObject->overwrittingRules[$key])) {
				return false;
			} else {
				return true;
			}
		}
		else //unsupported type
		{
			return true;
		}
	}

	public static function buildResponse($Status, $Message, $Data)
	{
		$retArray = [];
		$tempArray = [];
		$tempArray['Status'] = $Status;
		$tempArray['Message'] = $Message;
		
		$retArray['Result'] = $tempArray;
		$retArray['Data'] = $Data;
		
		return $retArray;
	}
}