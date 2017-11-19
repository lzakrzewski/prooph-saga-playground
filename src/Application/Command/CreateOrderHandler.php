<?php

declare(strict_types=1);

namespace Application\Command;

use Domain\Order\Order;
use Domain\Order\OrderRepository;

class CreateOrderHandler
{
    /** @var OrderRepository */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(CreateOrder $command)
    {
        $this->orderRepository->save(Order::create($command->orderId, $command->numberOfSeats));
    }
}
