<?php

namespace Helderjs\Component\DoctrineMongoODM;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Helderjs\Component\DoctrineMongoODM\Exception\InvalidConfigException;
use Interop\Container\ContainerInterface;

class ConnectionFactory
{
    /**
     * @param ContainerInterface $container
     * @return Connection
     * @throws InvalidConfigException for invalid config service values.
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->has('config') ? $container->get('config') : [];

        if (!isset($options['doctrine']) || !isset($options['doctrine']['connection'])) {
            return new Connection();
        }

        $default = !empty($options['doctrine']['default']) ? $options['doctrine']['default'] : 'odm_default';

        // Doctrine configuration options
        $options = $options['doctrine']['connection'];

        if (!isset($options[$default])) {
            throw new InvalidConfigException(sprintf('Doctrine configuration for %s not found.', $default));
        }

        try {
            $connectionString = isset($options[$default]['connection_string'])
                ? $options[$default]['connection_string'] : null;
            $dbName = null;
            if (empty($connectionString)) {
                $connectionString = 'mongodb://';
                $user = $options[$default]['user'];
                $password = $options[$default]['password'];
                $dbName = $options[$default]['dbname'];
                if ($user && $password) {
                    $connectionString .= $user . ':' . $password . '@';
                }
                $connectionString .= $options[$default]['server'] . ':' . $options[$default]['port'];
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

            return new Connection($connectionString, $options[$default]['options'], $configuration);
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }
}
