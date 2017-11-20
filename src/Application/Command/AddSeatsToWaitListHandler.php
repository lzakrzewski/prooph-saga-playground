<?php

declare(strict_types=1);

namespace Application\Command;

use Prooph\ServiceBus\EventBus;

class AddSeatsToWaitListHandler
{
    /** @var EventBus */
    private $eventBus;

    public function __construct(AddSeatsToWaitList $eventBus)
    {
        $this->eventBus  = $eventBus;
    }

    public function __invoke(AddSeatsToWaitList $command)
    {
        $this->eventBus->dispatch('sth');
    }
}
