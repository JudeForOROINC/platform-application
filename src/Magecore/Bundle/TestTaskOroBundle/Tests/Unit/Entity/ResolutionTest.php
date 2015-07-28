<?php

namespace Magecore\Bundle\TestTaskOroBundle\Tests\Unit\Entity;

use Magecore\Bundle\TestTaskOroBundle\Entity\Resolution;

class ResolutionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Resolution
     */
    protected $entity;

    protected function setUp()
    {
        $this->entity = new Resolution();
    }

    protected function tearDown()
    {
        unset($this->entity);
    }

    protected function setId($value)
    {
        $r = new \ReflectionClass($this->entity);
        $prop = $r->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($this->entity, $value);
        $prop->setAccessible(false);
    }

    public function testId()
    {
        $this->assertNull($this->entity->getId());
        $value = 1;
        $this->setId($value);
        $this->assertEquals($value, $this->entity->getId());
    }

    /**
     * @param $property
     * @param $value
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = $this->entity;

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        return array(
            array('value', 'Fixed'),
        );
    }
    public function testToString()
    {
        $entity = $this->entity;
        $entity->setValue('Fixed');
        $this->assertEquals($entity->getValue(), (string)$entity);
    }
}
