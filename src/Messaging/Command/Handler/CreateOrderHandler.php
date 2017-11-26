<?php

declare(strict_types=1);

namespace Messaging\Command\Handler;

use Messaging\Command\CreateOrder;
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

    public function __invoke(CreateOrder $command): void
    {
        $this->eventBus->dispatch(new OrderCreated($command->orderId, $command->numberOfSeats));
    }
}
