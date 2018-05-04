<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Test\Component\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Helderjs\Component\DoctrineMongoODM\YamlDriverFactory;
use Psr\Container\ContainerInterface;

class YamlDriverFactoryTest extends \PHPUnit_Framework_TestCase
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
        $factory = new YamlDriverFactory();

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
        $factory = new YamlDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => []]);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithEmptyDriverDoctrineConfig()
    {
        $factory = new YamlDriverFactory();

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
                        YamlDriver::class => [],
                    ],
                ],
            ],
        ];
        $factory = new YamlDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithYamlDriverDoctrineConfig()
    {
        $options = [
            'doctrine' => [
                'driver' => [
                    'odm_default' => [
                        YamlDriver::class => [
                            'simplified' => false,
                            'yml_dir' => [
                                '/path/to/files1',
                                '/path/to/files2',
                            ]
                        ],
                    ],
                ],
            ],
        ];
        $factory = new YamlDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);

        $driver = $factory($this->container->reveal());
        $this->assertInstanceOf(YamlDriver::class, $driver);
    }

    public function testCallingFactoryWithSimplifiedYamlDriverDoctrineConfig()
    {
        $options = [
            'doctrine' => [
                'driver' => [
                    'odm_default' => [
                        YamlDriver::class => [
                            'simplified' => true,
                            'yml_dir' => [
                                '/path/to/files1' => 'MyProject\Entities',
                                '/path/to/files2' => 'OtherProject\Entities'
                            ]
                        ],
                    ],
                ],
            ],
        ];
        $factory = new YamlDriverFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);

        $driver = $factory($this->container->reveal());
        $this->assertInstanceOf(YamlDriver::class, $driver);
    }
}
