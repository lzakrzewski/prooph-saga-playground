<?php

declare(strict_types=1);

namespace Messaging\ProcessManager;

use Messaging\DomainEvent;

abstract class Saga
{
    public function __invoke(DomainEvent $event): void
    {
        $method = sprintf('handleThat%s', (new \ReflectionClass($event))->getShortName());

        $this->$method($event);
    }
}
