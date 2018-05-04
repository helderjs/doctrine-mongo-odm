<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Psr\Container\ContainerInterface;

/**
 * Class AnnotationDriverFactory
 *
 * @package Helderjs\Component\DoctrineMongoODM
 */
class AnnotationDriverFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return AnnotationDriver
     * @throws InvalidConfigException for invalid config service values.
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $this->getDoctrineConfiguration($container, 'driver');

        if (empty($options[AnnotationDriver::class])) {
            throw new InvalidConfigException(sprintf('Doctrine driver configuration not found.'));
        }

        try {
            return AnnotationDriver::create($options[AnnotationDriver::class]['documents_dir']);
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }
}
