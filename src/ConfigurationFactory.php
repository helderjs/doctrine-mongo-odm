<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Types\Type;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

/**
 * Class ConfigurationFactory
 *
 * @package Helderjs\Component\DoctrineMongoODM
 */
class ConfigurationFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return Configuration
     * @throws InvalidConfigException for invalid config service values.
     */
    public function __invoke(ContainerInterface $container)
    {
        try {
            // Doctrine configuration options
            $options = $this->getDoctrineConfiguration($container, 'configuration');
            $config = new Configuration();

            if (empty($options)) {
                return $config;
            }

            // default db
            $config->setDefaultDB($options['default_db']);
            // proxies
            $config->setAutoGenerateProxyClasses($options['generate_proxies']);
            $config->setProxyDir($options['proxy_dir']);
            $config->setProxyNamespace($options['proxy_namespace']);
            // hydrators
            $config->setAutoGenerateHydratorClasses($options['generate_hydrators']);
            $config->setHydratorDir($options['hydrator_dir']);
            $config->setHydratorNamespace($options['hydrator_namespace']);
            // caching
            $metadataCache = isset($options['metadata_cache'])
                ? $container->get($options['metadata_cache'])
                : new ArrayCache();
            $config->setMetadataCacheImpl($metadataCache);
            // retries
            $config->setRetryConnect(
                isset($options['retry_connect']) ? $options['retry_connect'] : 0
            );
            $config->setRetryQuery(isset($options['retry_query']) ? $options['retry_query'] : 0);
            // the driver
            $config->setMetadataDriverImpl($container->get($options['driver']));
            // metadataFactory, if set
            if (isset($options['metadata_factory_name'])) {
                $config->setClassMetadataFactoryName($options['metadata_factory_name']);
            }
            // Register filters
            if (isset($options['filters'])) {
                foreach ($options['filters'] as $alias => $class) {
                    $config->addFilter($alias, $class);
                }
            }
            // custom types
            if (isset($options['filters'])) {
                foreach ($options['types'] as $name => $class) {
                    if (Type::hasType($name)) {
                        Type::overrideType($name, $class);
                    } else {
                        Type::addType($name, $class);
                    }
                }
            }
            // logger
            if (isset($options['logger'])) {
                $logger = is_callable($options['logger']) ? $options['logger']
                    : $container->get($options['logger']);
                $config->setLoggerCallable([$logger, 'log']);
            }
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }

        return $config;
    }
}
