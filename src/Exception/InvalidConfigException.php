<?php
/**
 * DoctrineMongoODM Component
 *
 * @see       https://github.com/helderjs/doctrine-mongo-odm
 * @copyright @copyright Copyright (c) 2016 Helder Santana
 * @license   https://github.com/helderjs/doctrine-mongo-odm/blob/master/LICENSE MIT License
 */
namespace Helderjs\Component\DoctrineMongoODM\Exception;

use DomainException;
use Interop\Container\Exception\ContainerException;

/**
 * Class InvalidConfigException
 *
 * @package Helderjs\Component\DoctrineMongoODM\Exception
 */
class InvalidConfigException extends DomainException implements ContainerException
{
}
