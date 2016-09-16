<?php

namespace Helders\Test\Component\DoctrineMongoODM\Asset;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Tests\Query\Filter\Filter;

class MyFilter extends Filter
{
    public function addFilterCriteria(ClassMetadata $targetDocument)
    {
        // Check if the entity implements the LocalAware interface
        if (!$targetDocument->reflClass->implementsInterface('LocaleAware')) {
            return array();
        }

        return array('locale' => $this->getParameter('locale'));
    }
}
