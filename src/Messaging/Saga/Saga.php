<?php

declare(strict_types=1);

namespace Messaging\Saga;

use Messaging\Command\MakeReservation;
use Messaging\Event\OrderCreated;
use Prooph\ServiceBus\CommandBus;
use Ramsey\Uuid\Uuid;

class Saga
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function handleThatOrderCreated(OrderCreated $orderCreated)
    {
        $this->commandBus->dispatch(
            new MakeReservation(Uuid::uuid4(), (int) $orderCreated->payload()['numberOfSeats'])
        );
    }
}
