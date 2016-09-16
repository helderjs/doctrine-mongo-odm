<?php

namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\DocumentManager;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

class DocumentManagerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return DocumentManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $this->getDoctrineConfiguration($container, 'documentmanager');

        if (empty($options)) {
            throw new InvalidConfigException(sprintf('Doctrine configuration not found.'));
        }

        $connection = $container->get($options['connection']);
        $configuration = $container->get($options['configuration']);
        $eventManager = isset($options['eventmanager']) ? $container->get($options['eventmanager']) : null;

        return DocumentManager::create($connection, $configuration, $eventManager);
    }
}
