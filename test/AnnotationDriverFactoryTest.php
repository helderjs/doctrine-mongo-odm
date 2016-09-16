<?php

namespace Helderjs\Test\Component\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Helderjs\Component\DoctrineMongoODM\AnnotationDriverFactory;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

class AnnotationDriverFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function testCallingFactoryWithNoConfigReturns()
    {
        $factory = new AnnotationDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(false);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());


        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([]);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());

        $this->container->has('doctrine')->willReturn(true);
        $this->container->get('doctrine')->willReturn([]);
        $this->container->has('config')->willReturn(true);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithEmptyDoctrineConfig()
    {
        $factory = new AnnotationDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => []]);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithEmptyDriverDoctrineConfig()
    {
        $factory = new AnnotationDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => ['driver' => []]]);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithWrongDriverDoctrineConfig()
    {
        $options = [
            'doctrine' => [
                'driver' => [
                    'odm_default' => [
                        AnnotationDriver::class => [],
                    ],
                ],
            ],
        ];
        $factory = new AnnotationDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithDriverDoctrineConfig()
    {
        $options = [
            'doctrine' => [
                'driver' => [
                    'odm_default' => [
                        AnnotationDriver::class => [
                            'documents_dir' => ['./src/myApp/Documents'],
                        ],
                    ],
                ],
            ],
        ];
        $factory = new AnnotationDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);

        $driver = $factory($this->container->reveal());
        $this->assertInstanceOf(AnnotationDriver::class, $driver);
    }
}
