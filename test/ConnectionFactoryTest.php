<?php

namespace Helderjs\Test\Component\DoctrineMongoODM;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Helderjs\Component\DoctrineMongoODM\ConnectionFactory;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
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
        $factory = new ConnectionFactory();

        $this->container->has('config')->willReturn(false);
        $connection = $factory($this->container->reveal());

        $this->assertInstanceOf(Connection::class, $connection);

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([]);
        $connection = $factory($this->container->reveal());

        $this->assertInstanceOf(Connection::class, $connection);
    }

    public function testCallingFactoryWithEmptyDoctrineConfig()
    {
        $factory = new ConnectionFactory();

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => []]);
        $connection = $factory($this->container->reveal());

        $this->assertInstanceOf(Connection::class, $connection);
    }

    public function testCallingFactoryWithWrongDoctrineConfig()
    {
        $factory = new ConnectionFactory();

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => ['connection' => []]]);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithMissingDoctrineConfig()
    {
        $options = [
            'doctrine' => [
                'default' => 'odm_default',
                'connection' => [
                    'odm_default' => [
                        'port' => '27017',
                    ],
                ],
            ],
        ];

        $factory = new ConnectionFactory();

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithDoctrineConfigConnectionString()
    {
        $options = [
            'doctrine' => [
                'default' => 'odm_default',
                'connection' => [
                    'odm_default' => [
                        'connection_string' => 'mongodb://username:password@localhost:27017/mydb',
                        'options' => [],
                    ],
                ],
            ],
        ];

        $configuration = $this->prophesize(Configuration::class);
        $factory = new ConnectionFactory();

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->container->has(Configuration::class)->willReturn(true);
        $this->container->get(Configuration::class)->willReturn($configuration->reveal());
        $connection = $factory($this->container->reveal());

        $this->assertInstanceOf(Connection::class, $connection);
    }

    public function testCallingFactoryWithDoctrineConfigParams()
    {
        $options = [
            'doctrine' => [
                'default' => 'odm_default',
                'connection' => [
                    'odm_default' => [
                        'server' => 'localhost',
                        'port' => '27017',
                        'user' => 'user',
                        'password' => 'password',
                        'dbname' => 'mydb',
                        'options' => [
                            'journal' => true,
                            'readPreference' => 'secondary',
                        ],
                    ],
                ],
            ],
        ];

        $configuration = $this->prophesize(Configuration::class);
        $factory = new ConnectionFactory();

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->container->has(Configuration::class)->willReturn(true);
        $this->container->get(Configuration::class)->willReturn($configuration->reveal());
        $connection = $factory($this->container->reveal());

        $this->assertInstanceOf(Connection::class, $connection);
    }

    public function testCallingFactoryWithDoctrineWithoutConfigurationClass()
    {
        $options = [
            'doctrine' => [
                'default' => 'odm_default',
                'connection' => [
                    'odm_default' => [
                        'server' => 'localhost',
                        'port' => '27017',
                        'user' => 'user',
                        'password' => 'password',
                        'dbname' => 'mydb',
                        'options' => [
                            'journal' => true,
                            'readPreference' => 'secondary',
                        ],
                    ],
                ],
            ],
        ];

        $factory = new ConnectionFactory();

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->container->has(Configuration::class)->willReturn(false);
        $connection = $factory($this->container->reveal());

        $this->assertInstanceOf(Connection::class, $connection);
    }
}
