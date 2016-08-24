<?php

namespace Skippy565\ObjectTranslationBundle\Tests\Controller;

use Skippy565\ObjectTranslationBundle\Interfaces\ObjectTranslation as ObjectTranslationInterface;
use Skippy565\ObjectTranslationBundle\Controller\ObjectTranslation as ObjectTranslation;

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