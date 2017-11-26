<?php

declare(strict_types=1);

namespace Messaging\Saga;

use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Messaging\Event\OrderConfirmed;
use Messaging\Event\OrderCreated;
use Messaging\Event\PaymentAccepted;
use Messaging\Event\SeatsNotReserved;
use Messaging\Event\SeatsReserved;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Ramsey\Uuid\Uuid;

//Todo: think about abstract saga
class Saga
{
    /** @var CommandBus */
    private $commandBus;

    /** @var EventBus */
    private $eventBus;

    /** @var StateRepository */
    private $stateRepository;

    public function __construct(CommandBus $commandBus, EventBus $eventBus, StateRepository $stateRepository)
    {
        $this->commandBus      = $commandBus;
        $this->eventBus        = $eventBus;
        $this->stateRepository = $stateRepository;
    }

    public function handleThatOrderCreated(OrderCreated $orderCreated)
    {
        $orderId       = $orderCreated->aggregateId();
        $reservationId = Uuid::uuid4();

        $this->stateRepository->save(
            State::create($orderId, $orderCreated->payload())
                ->apply(['reservationId' => $reservationId])
        );

        $this->commandBus->dispatch(
            new MakeReservation($reservationId, $orderId, (int) $orderCreated->payload()['numberOfSeats'])
        );
    }

    public function handleThatSeatsReserved(SeatsReserved $seatsReserved)
    {
        $state = $this->stateRepository->find(
            $orderId = Uuid::fromString($seatsReserved->payload()['orderId'])
        );

        if (null === $state) {
            return;
        }

        $paymentId = Uuid::uuid4();

        $this->stateRepository->save(
            State::create($orderId, $seatsReserved->payload())
                ->apply(['paymentId' => $paymentId])
        );

        $this->commandBus->dispatch(
            new MakePayment($paymentId, $orderId, (int) $seatsReserved->payload()['reservationAmount'])
        );
    }

    public function handleThatSeatsNotReserved(SeatsNotReserved $seatsNotReserved)
    {
        $state = $this->stateRepository->find(
            $orderId = Uuid::fromString($seatsNotReserved->payload()['orderId'])
        );

        if (null === $state) {
            return;
        }

        $this->commandBus->dispatch(
            new AddSeatsToWaitList(Uuid::uuid4(), (int) $seatsNotReserved->payload()['numberOfSeats'])
        );
    }

    public function handleThatPaymentAccepted(PaymentAccepted $paymentAccepted)
    {
        $state = $this->stateRepository->lastState();

        if (null === $state) {
            return;
        }

        $this->eventBus->dispatch(
            new OrderConfirmed(Uuid::fromString($state->payload()['orderId']), 5)
        );
    }
}
