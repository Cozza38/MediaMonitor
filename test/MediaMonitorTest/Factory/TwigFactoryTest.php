<?php

namespace MediaMonitorTest\Factory;

use MediaMonitor\Factory\TwigFactory;
use Twig_Environment;
use Zend\ServiceManager\ServiceManager;

/**
 *
 * @coversDefaultClass MediaMonitor\Factory\TwigFactory
 * @covers ::<!public>
 */
class TwigFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateServiceMustReturnTwigObject()
    {
        $config = array(
            'twig' => array()
        );
        $sl = $this->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $sl->expects($this->any())
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue($config));

        $factory = new TwigFactory;

        $twig = $factory->createService($sl);

        $this->assertInstanceOf('Twig_Environment', $twig);
    }

    public function testFactoryUsesConfig()
    {
    }
}

