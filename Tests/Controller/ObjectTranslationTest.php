<?php

namespace ObjectTranslationBundle\Tests\Controller;

use ObjectTranslationBundle\Interfaces\ObjectTranslation as ObjectTranslationInterface;
use ObjectTranslationBundle\Controller\ObjectTranslation as ObjectTranslation;

class ObjectTranslationTest 
{
	/*
	* @covers translate
	*/
	public function testTranslate()
	{
		$params = [
			'firstName'	=>	'TestFirst',
			'lastName'	=>	'TestLast',
			'email'		=>	'test@email.com'
		];
		/*$tempFromObject = new TestFromObject($params);

		$tempTranslationObject = new TestTranslationObject($tempFromObject);
		$result = ObjectTranslation::translate($tempTranslationObject);

		var_dump($result);*/
	}
}



class TestToObject
{
	protected $fullName;
	protected $email;
}

class TestFromObject
{
	protected $firstName;
	protected $lastName;
	protected $email;

	public function __construct($params = [])
	{
		foreach ($params as $key => $value) {
			$this->$key = $value;
		}
	}
}