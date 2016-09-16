# DoctrineMongoODM Component

[![Build Status](https://travis-ci.org/helderjs/doctrine-mongo-odm.svg?branch=master)](https://travis-ci.org/helderjs/doctrine-mongo-odm)

It's a component based on [DoctrineMongoODMModule](https://github.com/doctrine/DoctrineMongoODMModule) that provides [DoctrineMongoDbODM](http://docs.doctrine-project.org/projects/doctrine-mongodb-odm) integration for
several (Micro-)frameworks. The goal is be light and easy to configure, for that the library rely just on the [container-interop](https://github.com/container-interop/container-interop) and [MongoBD ODM](http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/). 

## Requirements

- PHP 5.6+
- [ext-mongo](https://pecl.php.net/package/mongodb) (if php version < 7)
- [mongo-php-adapter](https://github.com/alcaeus/mongo-php-adapter) (if php version >= 7)

ps.: if using [mongo-php-adapter](https://github.com/alcaeus/mongo-php-adapter) you should add and install it first in you project. After this the library will declare to composer that it provides the ext-mongo. 

We recommend using a dependency injection container, and typehint against [container-interop](https://github.com/container-interop/container-interop).

## Installation

Install this library using composer:

```bash
$ composer require helderjs/doctrine-mongo-odm
```

## Configuration

How to create the config file (What you should/can put in the config).

```php
return [
    'config' => [
        ...
        'doctrine' => [
            'default' => 'odm_default',
            'connection' => [
                'odm_default' => [
                    'server'           => 'localhost',
                    'port'             => '27017',
                    'user'             => 'myUser',
                    'password'         => 'myHardPassword',
                    'dbname'           => 'dbName',
                    'options'          => []
                ],
                'odm_secondary' => [
                    'connectionString' => 'mongodb://username:password@server2:27017/mydb',
                    'options'          => []
                ],
            ],
            'driver' => [
                'odm_default' => [
                    \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::class => [
                        'documents_dir' => ['./src/myApp/Documents']
                    ], 
                    \Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver::class => [
                        'simplified' => false,
                        'xml_dir' => [
                            '/path/to/files1',
                            '/path/to/files2',
                        ]
                     ],
                    \Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver::class => [
                        'simplified' => false,
                        'yml_dir' => [
                            '/path/to/files1',
                            '/path/to/files2',
                        ]
                    ],
                    \Doctrine\ODM\MongoDB\Mapping\Driver\MappingDriverChain::class => [
                        'Driver\Annotation' => \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::class,
                        'Driver\Xml' => \Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver::class,
                        'Driver\Yaml' => \Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver::class,
                    ],
                ],
            ],
            'configuration' => [
                'odm_default' => [
                    'metadata_cache'     => \Doctrine\Common\Cache\ArrayCache::class, // optional
                    'driver'             => \Doctrine\ODM\MongoDB\Mapping\Driver\MappingDriverChain::class,
                    'generate_proxies'   => true,
                    'proxy_dir'          => 'data/DoctrineMongoODMModule/Proxy',
                    'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',
                    'generate_hydrators' => true,
                    'hydrator_dir'       => 'data/DoctrineMongoODMModule/Hydrator',
                    'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',
                    'default_db'         => 'MyDBName',
                    'filters'            => [], // custom filters (optional)
                    'types'              => [], // custom types (optional)
                    'retry_connect'      => 0 // optional
                    'retry_query'        => 0 // optional
                    'logger'             => \MyLogger::calss \\ Logger implementation (optional)
                    'classMetadataFactoryName' => 'stdClass' \\ optional
                ]
            ],
            'documentmanager' => [
                'odm_default' => [
                    'connection'    => \Doctrine\ODM\MongoDB\Connection::class,
                    'configuration' => \Doctrine\ODM\MongoDB\Configuration::class,
                    'eventmanager'  => \Doctrine\ODM\MongoDB\EventManager::class, \\ optional
                ],
                'odm_secondary' => [
                    'connection'    => 'doctrine.connection.secondary',
                    'configuration' => \Doctrine\ODM\MongoDB\Configuration::class,
                    'eventmanager'  => 'doctrine.eventmanager.secondary', \\ optional
                ]
            ],
            'eventmanager' => [ \\ optional
                'odm_default' => [
                    'subscribers' => [
                        \MySubscriberImpl1::class,
                    ],
                ],
                'odm_secondary' => [
                    'subscribers' => [
                        new \MySubscriberImpl2(),
                    ],
                ],
            ],
        ],
        ...
    ],
];
```

Configuring DI at Zend Expressive
```php
...
'dependencies' => [
    'invokables' => [
        \Doctrine\Common\Cache\ArrayCache::class => \Doctrine\Common\Cache\ArrayCache::class,
        \MyLogger::class  => \MyLogger::class,
    ],
    'factories' => [
        \Doctrine\ODM\MongoDB\Configuration::class   => ConfigurationFactory::class,
        \Doctrine\ODM\MongoDB\Connection::class      => ConnectionFactory::class,
        \Doctrine\ODM\MongoDB\EventManager::class    => EventManagerFactory::class,
        \Doctrine\ODM\MongoDB\DocumentManager::class => DocumentManagerFactory::class,
        'doctrine.connection.secondary'              => new ConnectionFactory('odm_secondary'),
        'doctrine.eventmanager.secondary'            => new EventManagerFactory('odm_secondary'),
        'doctrine.documentmandager.secondary'        => new DocumentManagerFactory('odm_secondary'),
        \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::class   => \Helderjs\Component\DoctrineMongoODM\AnnotationDriverFactory::class,
        \Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver::class          => \Helderjs\Component\DoctrineMongoODM\AnnotationDriverFactory::class,
        \Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver::class         => \Helderjs\Component\DoctrineMongoODM\AnnotationDriverFactory::class,
        \Doctrine\ODM\MongoDB\Mapping\Driver\MappingDriverChain::class => \Helderjs\Component\DoctrineMongoODM\AnnotationDriverFactory::class,
    ],
 ],
 ...
```

SlimPHP

```php
$container['doctrine-connection'] = function ($container) {
    $factory = new ConnectionFactory();
    
    return $factory($container);
};

$container['doctrine-configuration'] = function ($container) {
    $factory = new ConfigurationFactory();
    
    return $factory($container);
};

$container['doctrine-eventmanager'] = function ($container) {
    $factory = new EventManagerFactory();
    
    return $factory($container);
};

$container['doctrine-driver'] = function ($container) {
    $factory = new MappingDriverChainFactory();
    
    return $factory($container);
};
```

## Goals

- Improve unit tests
- Improve documentation
- Implementing real examples
- ?introduce new features?

If you want to help: install, test it, report an issue, fork, open a pull request 

## License

The MIT License (MIT). Please see [License File](https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE) for more information.
