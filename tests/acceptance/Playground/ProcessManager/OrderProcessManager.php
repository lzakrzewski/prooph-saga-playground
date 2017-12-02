<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\ProcessManager;

use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Messaging\Event\OrderConfirmed;
use Messaging\Event\OrderPlaced;
use Messaging\Event\PaymentAccepted;
use Messaging\Event\SeatsNotReserved;
use Messaging\Event\SeatsReserved;
use Ramsey\Uuid\Uuid;
use tests\ScenarioTestCase;

class OrderProcessManager extends ScenarioTestCase
{
    /** @test */
    public function it_makes_a_seat_reservation_and_makes_a_payment_when_order_has_been_placed(): void
    {
        $this->scenario()
            ->when(new OrderPlaced($orderId = Uuid::uuid4(), 5))
            ->then(
                new MakeReservation($this->uuids()[1], $orderId, 5),
                new MakePayment($this->uuids()[2], $orderId, 500)
            );
    }

    /** @test */
    public function it_confirms_an_order_when_order_with_reservation_and_payments_are_successful(): void
    {
        $this->scenario()
            ->given(
                new OrderPlaced($orderId = Uuid::uuid4(), 5),
                new SeatsReserved(Uuid::uuid4(), $orderId, 5, 5 * 100)
            )
            ->when(new MakePayment(Uuid::uuid4(), $orderId, 500))
            ->then(new OrderConfirmed($orderId, 5));
    }

    /** @test */
    public function it_does_not_confirm_order_but_adds_to_wait_list_when_seats_were_not_reserved(): void
    {
        $this->scenario()
            ->given(
                new OrderPlaced($orderId = Uuid::uuid4(), 11)
            )
            ->when(new SeatsNotReserved(Uuid::uuid4(), $orderId, 11, 500))
            ->thenNot(new OrderConfirmed($orderId, 11))
            ->but(new AddSeatsToWaitList($this->uuids()[3], 11));
    }

    /** @test */
    public function it_does_not_confirm_an_order_when_order_does_not_exist(): void
    {
        $this->scenario()
            ->given(
                new SeatsReserved(Uuid::uuid4(), $orderId = Uuid::uuid4(), 5, 500)
            )
            ->when(new MakePayment(Uuid::uuid4(), $orderId, 500))
            ->thenNot(new OrderConfirmed($orderId, 5));
    }

    /** @test */
    public function it_can_handle_multiple_orders(): void
    {
        $this->scenario()
            ->given(
                new OrderPlaced($orderId1 = Uuid::uuid4(), 5),
                new SeatsReserved(Uuid::uuid4(), $orderId1, 5, 500),
                new PaymentAccepted(Uuid::uuid4(), $orderId1, 5, 500),
                new OrderPlaced($orderId2 = Uuid::uuid4(), 5),
                new SeatsReserved(Uuid::uuid4(), $orderId2, 5, 500)
            )
            ->when(new MakePayment(Uuid::uuid4(), $orderId2, 500))
            ->then(new OrderConfirmed($orderId2, 5));
    }
}
