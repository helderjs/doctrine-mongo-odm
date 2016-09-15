<?php

namespace Helderjs\Component\DoctrineMongoODM;

use Interop\Container\ContainerInterface;

abstract class AbstractFactory
{
    /**
     * The name of the configuration
     *
     * @var string
     */
    protected $default;

    /**
     * ConnectionFactory constructor.
     * Set the default configuration
     *
     * @param string $default
     */
    public function __construct($default = 'odm_default')
    {
        $this->default = $default;
    }

    /**
     * Get the configuration options
     *
     * @param ContainerInterface $container
     * @param $name
     * @return mixed
     */
    protected function getDoctrineConfiguration(ContainerInterface $container, $name)
    {
        $options = [];

        if ($container->has('doctrine')) {
            $options = $container->get('doctrine');
        }

        if ($container->has('config')) {
            $config = $container->get('config');

            if (isset($config['doctrine'])) {
                $options = $config['doctrine'];
            }
        }

        if (!empty($options) && isset($options[$name])) {
            $options = $options[$name];
        }

        return isset($options[$this->default]) ? $options[$this->default] : $options;
    }
}
