<?php

namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

class ConnectionFactory
{
    /**
     * The name of the configuration
     *
     * @var string
     */
    private $default;

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
    private function getDoctrineConfiguration(ContainerInterface $container, $name)
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

    /**
     * @param ContainerInterface $container
     * @return Connection
     * @throws InvalidConfigException for invalid config service values.
     */
    public function __invoke(ContainerInterface $container)
    {
        try {
            $options = $this->getDoctrineConfiguration($container, 'connection');

            if (empty($options)) {
                return new Connection();
            }

            $connectionString = isset($options['connection_string'])
                ? $options['connection_string'] : null;
            $dbName = null;
            if (empty($connectionString)) {
                $connectionString = 'mongodb://';
                $user = $options['user'];
                $password = $options['password'];
                $dbName = $options['dbname'];
                if ($user && $password) {
                    $connectionString .= $user . ':' . $password . '@';
                }
                $connectionString .= $options['server'] . ':' . $options['port'];
                if ($dbName) {
                    $connectionString .= '/' . $dbName;
                }
            } else {
                // parse dbName from the connectionString
                $dbStart = strpos($connectionString, '/', 11);
                if (false !== $dbStart) {
                    $dbEnd = strpos($connectionString, '?');
                    $dbName = substr(
                        $connectionString,
                        $dbStart + 1,
                        $dbEnd ? ($dbEnd - $dbStart - 1) : PHP_INT_MAX
                    );
                }
            }

            $configuration = null;
            if ($container->has(Configuration::class)) {
                /** @var $configuration \Doctrine\ODM\MongoDB\Configuration */
                $configuration = $container->get(Configuration::class);
                // Set defaultDB to $dbName, if it's not defined in configuration
                if (null === $configuration->getDefaultDB()) {
                    $configuration->setDefaultDB($dbName);
                }
            }

            return new Connection($connectionString, $options['options'], $configuration);
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }
}
