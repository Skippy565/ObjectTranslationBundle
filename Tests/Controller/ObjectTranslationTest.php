<?php

namespace ObjectTranslationBundle\Tests\Controller;

use Skippy565\ObjectTranslationBundle\Interfaces\ObjectTranslation as ObjectTranslationInterface;
use Skippy565\ObjectTranslationBundle\Controller\ObjectTranslation as ObjectTranslation;

include_once(dirname(__FILE__).'/../../vendor/autoload.php');

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
	}
}