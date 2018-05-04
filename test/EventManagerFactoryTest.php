<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Test\Component\DoctrineMongoODM;

use Doctrine\Common\EventManager;
use Helderjs\Component\DoctrineMongoODM\EventManagerFactory;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Helderjs\Test\Component\DoctrineMongoODM\Asset\TestEventSubscriber;
use Helderjs\Test\Component\DoctrineMongoODM\Asset\TestEventSubscriber2;
use Helderjs\Test\Component\DoctrineMongoODM\Asset\TestEventSubscriber3;
use Psr\Container\ContainerInterface;

class EventManagerFactoryTest extends \PHPUnit_Framework_TestCase
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
        $factory = new EventManagerFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(false);
        $eventManager = $factory($this->container->reveal());
        $this->assertInstanceOf(EventManager::class, $eventManager);

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([]);
        $eventManager = $factory($this->container->reveal());
        $this->assertInstanceOf(EventManager::class, $eventManager);

        $this->container->has('doctrine')->willReturn(false);
        $this->container->get('doctrine')->willReturn([]);
        $this->container->has('config')->willReturn(true);
        $eventManager = $factory($this->container->reveal());
        $this->assertInstanceOf(EventManager::class, $eventManager);
    }

    public function testCallingFactoryWithEmptyDoctrineConfig()
    {
        $factory = new EventManagerFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => []]);
        $eventManager = $factory($this->container->reveal());

        $this->assertInstanceOf(EventManager::class, $eventManager);
    }

    public function testCallingFactoryWithWrongDoctrineConfig()
    {
        $factory = new EventManagerFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn(['doctrine' => ['eventmanager' => []]]);
        $eventManager = $factory($this->container->reveal());

        $this->assertInstanceOf(EventManager::class, $eventManager);
    }

    public function testCallingFactoryWithDoctrineConfigWrongSubscriber()
    {
        $options = $options = [
            'doctrine' => [
                'default' => 'odm_default',
                'eventmanager' => [
                    'odm_default' => [
                        'subscribers' => [
                            true,
                        ],
                    ],
                ],
            ],
        ];
        $factory = new EventManagerFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCallingFactoryWithDoctrineConfig()
    {
        $subscriber = $this->prophesize(TestEventSubscriber::class);

        $options = $options = [
            'doctrine' => [
                'default' => 'odm_default',
                'eventmanager' => [
                    'odm_default' => [
                        'subscribers' => [
                            TestEventSubscriber::class,
                            TestEventSubscriber2::class,
                            new TestEventSubscriber3(),
                        ],
                    ],
                ],
            ],
        ];
        $factory = new EventManagerFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->container->has(TestEventSubscriber::class)->willReturn(true);
        $this->container->get(TestEventSubscriber::class)->willReturn($subscriber->reveal());
        $this->container->has(TestEventSubscriber2::class)->willReturn(false);
        $this->container->has(TestEventSubscriber3::class)->willReturn(false);
        $eventManager = $factory($this->container->reveal());

        $this->assertInstanceOf(EventManager::class, $eventManager);
    }
}
