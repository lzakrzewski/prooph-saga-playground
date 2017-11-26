<?php

declare(strict_types=1);

namespace Messaging\Command\Handler;

use Messaging\Command\PlaceOrder;
use Messaging\Event\OrderPlaced;
use Prooph\ServiceBus\EventBus;

class PlaceOrderHandler
{
    /** @var EventBus */
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus  = $eventBus;
    }

    public function __invoke(PlaceOrder $command): void
    {
        $this->eventBus->dispatch(new OrderPlaced($command->orderId, $command->numberOfSeats));
    }
}
