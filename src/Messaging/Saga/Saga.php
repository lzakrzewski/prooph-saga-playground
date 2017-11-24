<?php

declare(strict_types=1);

namespace Messaging\Saga;

use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Messaging\Event\OrderConfirmed;
use Messaging\Event\OrderCreated;
use Messaging\Event\PaymentAccepted;
use Messaging\Event\SeatsReserved;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Ramsey\Uuid\Uuid;

class Saga
{
    /** @var CommandBus */
    private $commandBus;

    /** @var EventBus */
    private $eventBus;

    public function __construct(CommandBus $commandBus, EventBus $eventBus)
    {
        $this->commandBus = $commandBus;
        $this->eventBus   = $eventBus;

        //Todo: temporary hack
        $this->state = [];
    }

    public function handleThatOrderCreated(OrderCreated $orderCreated)
    {
        $this->state['orderId'] = $orderCreated->aggregateId();

        $this->commandBus->dispatch(
            new MakeReservation(Uuid::uuid4(), (int) $orderCreated->payload()['numberOfSeats'])
        );
    }

    public function handleThatSeatsReserved(SeatsReserved $seatsReserved)
    {
        $this->commandBus->dispatch(
            new MakePayment(Uuid::uuid4(), (int) $seatsReserved->payload()['numberOfSeats'])
        );
    }

    public function handleThatPaymentAccepted(PaymentAccepted $paymentAccepted)
    {
        if (false === isset($this->state['orderId'])) {
            return;
        }

        $this->eventBus->dispatch(
            new OrderConfirmed($this->state['orderId'], 5)
        );
    }
}
