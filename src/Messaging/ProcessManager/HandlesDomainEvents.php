<?php

declare(strict_types=1);

namespace Messaging\ProcessManager;

use Messaging\DomainEvent;

trait HandlesDomainEvents
{
    public function __invoke(DomainEvent $event): void
    {
        $this->handleThat($event);
    }

    public function handleThat(DomainEvent $event): void
    {
        $method = sprintf('handleThat%s', (new \ReflectionClass($event))->getShortName());

        if (false === method_exists($this, $method)) {
            throw new \BadMethodCallException(
                sprintf(
                    'Method %s does not exist on class %s',
                    $method,
                    get_class($this)
                )
            );
        }

        $this->$method($event);
    }
}
