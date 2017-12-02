<?php

declare(strict_types=1);

namespace Messaging\ProcessManager;

use Messaging\DomainEvent;

interface ProcessManager
{
    public function handleThat(DomainEvent $domainEvent);
}
