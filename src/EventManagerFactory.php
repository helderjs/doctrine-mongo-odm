<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

/**
 * Class EventManagerFactory
 *
 * @package Helderjs\Component\DoctrineMongoODM
 */
class EventManagerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return EventManager
     * @throws InvalidConfigException for invalid config service values.
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $this->getDoctrineConfiguration($container, 'eventmanager');
        $eventManager = new EventManager();

        if (empty($options)) {
            return $eventManager;
        }

        try {
            foreach ($options['subscribers'] as $subscriberName) {
                $subscriber = $subscriberName;
                if (is_string($subscriber)) {
                    if ($container->has($subscriber)) {
                        $subscriber = $container->get($subscriber);
                    } elseif (class_exists($subscriber)) {
                        $subscriber = new $subscriber();
                    }
                }
                if ($subscriber instanceof EventSubscriber) {
                    $eventManager->addEventSubscriber($subscriber);
                    continue;
                }
                $subscriberType = is_object($subscriberName) ? get_class($subscriberName) : $subscriberName;
                throw new InvalidConfigException(
                    sprintf(
                        'Invalid event subscriber "%s" given, must be a service name, '
                        . 'class name or an instance implementing Doctrine\Common\EventSubscriber',
                        is_string($subscriberType) ? $subscriberType : gettype($subscriberType)
                    )
                );
            }
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }

        return $eventManager;
    }
}
