<?php

namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

class XmlDriverFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return XmlDriver|SimplifiedXmlDriver
     * @throws InvalidConfigException for invalid config service values.
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $this->getDoctrineConfiguration($container, 'driver');

        if (empty($options)) {
            throw new InvalidConfigException(sprintf('Doctrine driver configuration not found.'));
        }

        try {
            if ($options[XmlDriver::class]['simplified']) {
                return new SimplifiedXmlDriver($options[XmlDriver::class]['xml_dir']);
            }

            return new XmlDriver($options[XmlDriver::class]['xml_dir']);
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }
}
