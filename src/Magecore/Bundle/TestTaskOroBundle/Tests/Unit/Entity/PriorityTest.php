<?php

namespace Magecore\Bundle\TestTaskOroBundle\Tests\Unit\Entity;

use Magecore\Bundle\TestTaskOroBundle\Entity\Priority;

class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Priority
     */
    protected $entity;

    protected function setUp()
    {
        $this->entity = new Priority('name');
    }

    protected function tearDown()
    {
        unset($this->entity);
    }

    protected function setId($value)
    {
        $r = new \ReflectionClass($this->entity);
        $prop = $r->getProperty('name');
        $prop->setAccessible(true);
        $prop->setValue($this->entity, $value);
        $prop->setAccessible(false);
    }

    public function testId()
    {
        $this->assertEquals('name', $this->entity->getName());
        $value = 'low';
        $this->setId($value);
        $this->assertEquals($value, $this->entity->getName());
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
            array('order', 2),
            array('label', 'Low priority'),
        );
    }

    public function testToString()
    {
        $entity = $this->entity;
        $entity->setLabel('Low priority');
        $this->assertEquals($entity->getLabel(), (string)$entity);
    }
}
