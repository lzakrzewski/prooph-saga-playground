<?php

declare(strict_types=1);

namespace Application\Command;

use Prooph\ServiceBus\EventBus;

class MakeReservationHandler
{
    /** @var EventBus */
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus  = $eventBus;
    }

    public function __invoke(MakeReservation $command)
    {
        $this->eventBus->dispatch('sth');
    }
}
