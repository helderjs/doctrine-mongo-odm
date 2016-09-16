<?php

namespace Helderjs\Test\Component\DoctrineMongoODM\Asset;

use Doctrine\Common\EventSubscriber;

class TestEventSubscriber implements EventSubscriber
{
    const PRE_FOO = 'preFoo';

    public $preFooInvoked = false;

    public function preFoo()
    {
        $this->preFooInvoked = true;
    }

    public function getSubscribedEvents()
    {
        return array(self::PRE_FOO);
    }
}
