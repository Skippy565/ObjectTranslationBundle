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
    public function testTranslate()
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
            $this->assertEquals($fromObject->__get($key), $data->__get($key), "From object $key does not match to object $value");
        }

        foreach ($translationObject->mappingFunctions as $property => $method) {
            $this->assertObjectHasAttribute($property, $translationObject->toObject, "Property $property not found in to object");
            $this->assertEquals($fromObject->__get('firstName') . ' ' . $fromObject->__get('lastName'), $data->__get($property));
        }
    }
}
