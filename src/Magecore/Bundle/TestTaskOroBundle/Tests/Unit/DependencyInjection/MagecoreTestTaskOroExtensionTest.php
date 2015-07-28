<?php

namespace Magecore\Bundle\TestTaskOroBundle\Tests\Unit\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;

use Magecore\Bundle\TestTaskOroBundle\DependencyInjection\MagecoreTestTaskOroExtension;

class MagecoreTestTaskOroExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $expectedDefinitions = array(
        'magecore_testtaskoro.issue_manager.api',
        'magecore_testtaskoro.form.type.issue',
        'orocrm_task.form',
    );

    /**
     * @var array
     */
    protected $expectedParameters = array(
        'magecore_testtaskoro.form.type.task.class',
    );

    public function testLoad()
    {
        $actualDefinitions = array();
        $actualParameters  = array();

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(array('setDefinition', 'setParameter'))
            ->getMock();
        $container->expects($this->any())
            ->method('setDefinition')
            ->will(
                $this->returnCallback(
                    function ($id, Definition $definition) use (&$actualDefinitions) {
                        $actualDefinitions[$id] = $definition;
                    }
                )
            );
        $container->expects($this->any())
            ->method('setParameter')
            ->will(
                $this->returnCallback(
                    function ($name, $value) use (&$actualParameters) {
                        $actualParameters[$name] = $value;
                    }
                )
            );

        $extension = new MagecoreTestTaskOroExtension();
        $extension->load(array(), $container);
        //var_dump($actualDefinitions);
        //var_dump(array_keys($actualParameters));

        foreach ($this->expectedDefinitions as $serviceId) {
            $this->assertArrayHasKey($serviceId, $actualDefinitions);
            $this->assertNotEmpty($actualDefinitions[$serviceId]);
        }

        foreach ($this->expectedParameters as $parameterName) {
            $this->assertArrayHasKey($parameterName, $actualParameters);
            $this->assertNotEmpty($actualParameters[$parameterName]);
        }
    }
}
