<?php

declare(strict_types=1);

namespace Application\Command;

use Domain\Order\OrderCreated;
use Domain\Order\OrderRepository;
use Prooph\ServiceBus\EventBus;

class CreateOrderHandler
{
    /** @var OrderRepository */
    private $orderRepository;

    /** @var EventBus */
    private $eventBus;

    public function __construct(OrderRepository $orderRepository, EventBus $eventBus)
    {
        $this->orderRepository = $orderRepository;
        $this->eventBus        = $eventBus;
    }

    public function __invoke(CreateOrder $command)
    {
        $this->eventBus->dispatch(new OrderCreated($command->orderId, $command->numberOfSeats));
    }
}
