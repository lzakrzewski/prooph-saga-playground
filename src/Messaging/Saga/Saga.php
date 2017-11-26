<?php

declare(strict_types=1);

namespace Messaging\Saga;

use Messaging\DomainEvent;

abstract class Saga
{
    public function __invoke(DomainEvent $event)
    {
        $method = sprintf('handleThat%s', (new \ReflectionClass($event))->getShortName());

        $this->$method($event);
    }
}
