<?php

namespace Helderjs\Component\DoctrineMongoODM\Exception;

use DomainException;
use Interop\Container\Exception\ContainerException;

class InvalidConfigException extends DomainException implements ContainerException
{
}
