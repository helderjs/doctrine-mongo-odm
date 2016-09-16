<?php

namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

class YamlDriverFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return YamlDriver|SimplifiedYamlDriver
     * @throws InvalidConfigException for invalid config service values.
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $this->getDoctrineConfiguration($container, 'driver');

        if (empty($options)) {
            throw new InvalidConfigException(sprintf('Doctrine driver configuration not found.'));
        }

        try {
            if ($options[YamlDriver::class]['simplified']) {
                return new SimplifiedYamlDriver($options[YamlDriver::class]['yml_dir']);
            }

            return new YamlDriver($options[YamlDriver::class]['yml_dir']);
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }
}
