<?php

namespace Helderjs\Test\Component\DoctrineMongoODM;

use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Repository\DefaultRepositoryFactory;
use Helderjs\Component\DoctrineMongoODM\DocumentManagerFactory;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

class DocumentManagerFactoryTest extends \PHPUnit_Framework_TestCase
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
        $factory = new DocumentManagerFactory();

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(false);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());

        $this->container->has('doctrine')->willReturn(true);
        $this->container->get('doctrine')->willReturn([]);
        $this->container->has('config')->willReturn(false);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([]);
        $this->expectException(InvalidConfigException::class);
        $factory($this->container->reveal());
    }

    public function testCreationWithConfiguration()
    {
        $options = [
            'doctrine' => [
                'documentmanager' => [
                    'odm_default' => [
                        'connection'    => Connection::class,
                        'configuration' => Configuration::class,
                        'eventmanager'  => EventManager::class,
                    ],
                ],
            ],
        ];

        $connection = $this->prophesize(Connection::class);
        $configuration = $this->prophesize(Configuration::class);
        $eventManager = $this->prophesize(EventManager::class);

        $configuration->getClassMetadataFactoryName()->willReturn(ClassMetadataFactory::class);
        $configuration->getMetadataCacheImpl()->willReturn(null);
        $configuration->getRepositoryFactory()->willReturn(DefaultRepositoryFactory::class);
        $configuration->getHydratorDir()->willReturn('/tmp/hydrator');
        $configuration->getHydratorNamespace()->willReturn('\Hydarator');
        $configuration->getAutoGenerateHydratorClasses()->willReturn(false);
        $configuration->getProxyDir()->willReturn('/tmp/proxy');
        $configuration->getProxyNamespace()->willReturn('\Proxy');
        $configuration->getAutoGenerateProxyClasses()->willReturn(false);

        $this->container->has('doctrine')->willReturn(false);
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($options);
        $this->container->get(Connection::class)->willReturn($connection->reveal());
        $this->container->get(Configuration::class)->willReturn($configuration->reveal());
        $this->container->get(EventManager::class)->willReturn($eventManager->reveal());

        $factory = new DocumentManagerFactory();
        $documentManager = $factory($this->container->reveal());

        $this->assertInstanceOf(DocumentManager::class, $documentManager);
    }
}
