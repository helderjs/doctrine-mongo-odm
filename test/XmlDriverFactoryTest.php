<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Test\Component\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Helderjs\Component\DoctrineMongoODM\XmlDriverFactory;
use Psr\Container\ContainerInterface;

class XmlDriverFactoryTest extends \PHPUnit_Framework_TestCase
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
        $factory = new XmlDriverFactory();

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
        $this->container->get('config')->willReturn([]);
        $this->container->has('config')->willReturn(false);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithEmptyDoctrineConfig()
    {
        $factory = new XmlDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => []]);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithEmptyDriverDoctrineConfig()
    {
        $factory = new XmlDriverFactory();

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
                        XmlDriver::class => [],
                    ],
                ],
            ],
        ];
        $factory = new XmlDriverFactory();

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
                        XmlDriver::class => [
                            'simplified' => false,
                            'xml_dir' => [
                                '/path/to/files1',
                                '/path/to/files2',
                            ]
                        ],
                    ],
                ],
            ],
        ];
        $factory = new XmlDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);

        $driver = $factory($this->container->reveal());
        $this->assertInstanceOf(XmlDriver::class, $driver);
    }

    public function testCallingFactoryWithSimplifiedXmlDriverDoctrineConfig()
    {
        $options = [
            'doctrine' => [
                'driver' => [
                    'odm_default' => [
                        XmlDriver::class => [
                            'simplified' => true,
                            'xml_dir' => [
                                '/path/to/files1' => 'MyProject\Entities',
                                '/path/to/files2' => 'OtherProject\Entities'
                            ]
                        ],
                    ],
                ],
            ],
        ];
        $factory = new XmlDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);

        $driver = $factory($this->container->reveal());
        $this->assertInstanceOf(XmlDriver::class, $driver);
    }
}
