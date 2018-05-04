<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Psr\Container\ContainerInterface;

/**
 * Class MappingDriverChainFactory
 *
 * @package Helderjs\Component\DoctrineMongoODM
 */
class MappingDriverChainFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return MappingDriverChain
     * @throws InvalidConfigException for invalid config service values.
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $this->getDoctrineConfiguration($container, 'driver');

        if (empty($options)) {
            throw new InvalidConfigException(sprintf('Doctrine driver configuration not found.'));
        }

        try {
            $driverChain = new MappingDriverChain();

            foreach ($options[MappingDriverChain::class] as $namespace => $driver) {
                $driverChain->addDriver($container->get($driver), $namespace);
            }

            $driverChain->setDefaultDriver(
                $container->get(reset($options[MappingDriverChain::class]))
            );

            return $driverChain;
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }
}
