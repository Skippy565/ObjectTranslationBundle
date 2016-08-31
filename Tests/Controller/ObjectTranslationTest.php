<?php

namespace Skippy565\ObjectTranslationBundle\Tests\Controller;

use Skippy565\ObjectTranslationBundle\Controller\AbstractObjectTranslationController;

/**
 * Class ObjectTranslationTest
 * @package Skippy565\ObjectTranslationBundle\Tests\Controller
 */
class ObjectTranslationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers AbstractObjectTranslation::translate
     */
    public function testTranslateObjectToObject()
    {
        // instantiate from object
        $fromObject = new FromTestObject();

        // instantiate to object
        $toObject = new ToTestObject();

        // instantiate translation object
        $translationObject = new TranslationTestObject($fromObject, $toObject);

        // translate
        $result = AbstractObjectTranslationController::translate($translationObject);

        // verify translation result
        $this->assertArrayHasKey('result', $result, 'Result key missing from translation result.');
        $this->assertArrayHasKey('data', $result, 'Data key missing from translation result.');
        $this->assertArraySubset(['status' => 'true', 'message' => 'Mapped'], $result['result'], true, 'Result is missing status or message key.');

        /** @var ToTestObject $data */
        $data = $result['data'];

        // verify translation data
        $this->assertInstanceOf(ToTestObject::class, $data, 'Unexpected data instance.');

        foreach ($translationObject->translationModel as $key => $value) {
        	if ($value != 'noOverride') {
            	$this->assertEquals($fromObject->__get($key), $data->__get($value), "From object $key does not match to object $value");
            }
        }

        //check no override value
        $this->assertNotEquals($fromObject->__get('email'), $data->__get('noOverride'));

        foreach ($translationObject->mappingFunctions as $property => $method) {
            $this->assertObjectHasAttribute($property, $translationObject->toObject, "Property $property not found in to object");
            $this->assertEquals($fromObject->__get('firstName') . ' ' . $fromObject->__get('lastName'), $data->__get($property));
        }
    }

    /**
     * @covers AbstractObjectTranslation::translate
     */
    public function testTranslateObjectToArray()
    {
        // instantiate from object
        $fromObject = new FromTestObject();

        // instantiate to object
        $toObject = json_decode(json_encode(new ToTestObject()), true);

        // instantiate translation object
        $translationObject = new TranslationTestObject($fromObject, $toObject);

        // translate
        $result = AbstractObjectTranslationController::translate($translationObject);

        // verify translation result
        $this->assertArrayHasKey('result', $result, 'Result key missing from translation result.');
        $this->assertArrayHasKey('data', $result, 'Data key missing from translation result.');
        $this->assertArraySubset(['status' => 'true', 'message' => 'Mapped'], $result['result'], true, 'Result is missing status or message key.');

        /** @var ToTestObject $data */
        $data = $result['data'];

        // verify translation data
        $this->assertEquals(is_array($data), true);

        foreach ($translationObject->translationModel as $key => $value) {
        	if ($value != 'noOverride') {
            	$this->assertEquals($fromObject->__get($key), $data[$value], "From object $key does not match to object $value");
            }
        }

        //check value that is not to be overridden
        $this->assertNotEquals($fromObject->__get('email'), $data['noOverride']);

        foreach ($translationObject->mappingFunctions as $property => $method) {
            $this->assertTrue(isset($translationObject->toObject[$property]), true, "Property $property not found in to object");
            $this->assertEquals($fromObject->__get('firstName') . ' ' . $fromObject->__get('lastName'), $data[$property]);
        }
    }

    /**
     * @covers AbstractObjectTranslation::translate
     */
    public function testTranslateArrayToArray()
    {
        // instantiate from object
        $fromObject = json_decode(json_encode(new FromTestObject()), true);

        // instantiate to object
        $toObject = json_decode(json_encode(new ToTestObject()), true);

        // instantiate translation object
        $translationObject = new TranslationTestObject($fromObject, $toObject);

        // translate
        $result = AbstractObjectTranslationController::translate($translationObject);

        // verify translation result
        $this->assertArrayHasKey('result', $result, 'Result key missing from translation result.');
        $this->assertArrayHasKey('data', $result, 'Data key missing from translation result.');
        $this->assertArraySubset(['status' => 'true', 'message' => 'Mapped'], $result['result'], true, 'Result is missing status or message key.');

        /** @var ToTestObject $data */
        $data = $result['data'];

        // verify translation data
        $this->assertEquals(is_array($data), true);

        foreach ($translationObject->translationModel as $key => $value) {
        	if ($value != 'noOverride') {
            	$this->assertEquals($fromObject[$key], $data[$value], "From object $key does not match to object $value");
            }
        }

        //check value that is not to be overridden
        $this->assertNotEquals($fromObject['email'], $data['noOverride']);

        foreach ($translationObject->mappingFunctions as $property => $method) {
            $this->assertTrue(isset($translationObject->toObject[$property]), true, "Property $property not found in to object");
            $this->assertEquals($fromObject['firstName'] . ' ' . $fromObject['lastName'], $data[$property]);
        }
    }
}
