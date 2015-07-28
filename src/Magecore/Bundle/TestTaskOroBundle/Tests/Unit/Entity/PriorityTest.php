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
        $this->entity = new Priority();
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

}
