<?php

namespace ObjectTranslationBundle\Tests\Controller;

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