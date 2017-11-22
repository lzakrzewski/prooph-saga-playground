<?php

declare(strict_types=1);

namespace Messaging\Command\Handler;

use Messaging\Command\AddSeatsToWaitList;
use Messaging\Event\SeatsAddedToWaitList;
use Prooph\ServiceBus\EventBus;

class AddSeatsToWaitListHandler
{
    /** @var EventBus */
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus  = $eventBus;
    }

    public function __invoke(AddSeatsToWaitList $command)
    {
        $this->eventBus->dispatch(new SeatsAddedToWaitList($command->waitListId, $command->numberOfSeats));
    }
}
