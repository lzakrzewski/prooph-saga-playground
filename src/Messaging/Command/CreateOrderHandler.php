<?php

declare(strict_types=1);

namespace Messaging\Command;

use Messaging\Event\OrderCreated;
use Prooph\ServiceBus\EventBus;

class CreateOrderHandler
{
    /** @var EventBus */
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus  = $eventBus;
    }

    public function __invoke(CreateOrder $command)
    {
        $this->eventBus->dispatch(new OrderCreated($command->orderId, $command->numberOfSeats));
    }
}
