<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Test\Component\DoctrineMongoODM;

use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Helderjs\Component\DoctrineMongoODM\MappingDriverChainFactory;
use Interop\Container\ContainerInterface;

class MappingDriverChainFactoryTest extends \PHPUnit_Framework_TestCase
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
        $factory = new MappingDriverChainFactory();

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
        $this->container->has('config')->willReturn(false);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithEmptyDoctrineConfig()
    {
        $factory = new MappingDriverChainFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => []]);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithEmptyDriverDoctrineConfig()
    {
        $factory = new MappingDriverChainFactory();

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
                        MappingDriverChain::class => [],
                    ],
                ],
            ],
        ];
        $factory = new MappingDriverChainFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithMissedDriverDoctrineConfig()
    {
        $options = [
            'doctrine' => [
                'driver' => [
                    'odm_default' => [
                        MappingDriverChain::class => [
                            AnnotationDriver::class
                        ],
                    ],
                ],
            ],
        ];
        $factory = new MappingDriverChainFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithXmlDriverDoctrineConfig()
    {
        $options = [
            'doctrine' => [
                'driver' => [
                    'odm_default' => [
                        MappingDriverChain::class => [
                            AnnotationDriver::class,
                            XmlDriver::class,
                        ],
                    ],
                ],
            ],
        ];
        $factory = new MappingDriverChainFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $annotation = $this->prophesize(AnnotationDriver::class);
        $this->container->get(AnnotationDriver::class)->willReturn($annotation->reveal());
        $xml = $this->prophesize(XmlDriver::class);
        $this->container->get(XmlDriver::class)->willReturn($xml->reveal());

        $driver = $factory($this->container->reveal());
        $this->assertInstanceOf(MappingDriverChain::class, $driver);
    }
}
