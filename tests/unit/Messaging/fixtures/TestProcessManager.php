<?php

declare(strict_types=1);

namespace tests\unit\Messaging\fixtures;

use Messaging\DomainEvent;
use Messaging\ProcessManager\HandlesDomainEvents;
use Messaging\ProcessManager\ProcessManager;

class TestProcessManager implements ProcessManager
{
    use HandlesDomainEvents;

    private $handledEvents = [];

    public function handleThatTestMessage(DomainEvent $domainEvent): void
    {
        $this->handledEvents[] = $domainEvent;
    }

    public function handleThatTestDomainEvent1(DomainEvent $domainEvent): void
    {
        $this->handledEvents[] = $domainEvent;
    }

    public function handledDomainEvents(): array
    {
        return $this->handledEvents;
    }
}
