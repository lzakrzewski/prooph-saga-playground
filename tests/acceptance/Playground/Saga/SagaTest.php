<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\Saga;

use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Messaging\Event\OrderConfirmed;
use Messaging\Event\OrderCreated;
use Messaging\Event\PaymentAccepted;
use Messaging\Event\SeatsNotReserved;
use Messaging\Event\SeatsReserved;
use Ramsey\Uuid\Uuid;
use tests\UsesScenarioTestCase;

class SagaTest extends UsesScenarioTestCase
{
    /** @test */
    public function it_makes_a_seat_reservation_and_makes_a_payment_when_order_has_been_created()
    {
        $this->scenario()
            ->when(new OrderCreated($orderId = Uuid::uuid4(), 5))
            ->then(
                new MakeReservation($this->aggregateIds()[1], $orderId, 5),
                new MakePayment($this->aggregateIds()[2], $orderId, 500)
            );
    }

    /** @test */
    public function it_confirms_an_order_when_order_with_reservation_and_payments_are_successful()
    {
        $this->scenario()
            ->given(
                new OrderCreated($orderId = Uuid::uuid4(), 5),
                new SeatsReserved(Uuid::uuid4(), $orderId, 5, 500)
            )
            ->when(new MakePayment(Uuid::uuid4(), $orderId, 500))
            ->then(new OrderConfirmed($orderId, 5));
    }

    /** @test */
    public function it_does_not_confirm_order_but_adds_to_wait_list_when_seats_were_not_reserved()
    {
        $this->markTestSkipped();

        $this->scenario()
            ->given(
                new OrderCreated($orderId = Uuid::uuid4(), 5)
            )
            ->when(new SeatsNotReserved(Uuid::uuid4(), 5, 500))
            ->thenNot(new OrderConfirmed($orderId, 5))
            ->but(new AddSeatsToWaitList($this->aggregateIds()[3], 5));
    }

    //todo : when order was not created
    //Todo: when no reservation
    //todo: duplicate events

    /** @test */
    public function it_does_not_confirm_order_when_seats_has_not_been_reserved()
    {
        $this->markTestIncomplete();

        $this->scenario()
            ->given(
                new OrderCreated($orderId = Uuid::uuid4(), 5),
                new SeatsNotReserved(Uuid::uuid4(), $orderId, 5, 500)
            )
            ->when(new PaymentAccepted($this->aggregateIds()[2], $orderId, 5, 5 * 100))
            ->thenNot(new OrderConfirmed($orderId = Uuid::uuid4(), 5));
    }

    public function test_test()
    {
        $this->markTestIncomplete('Waitlist scenario');
    }

    /** @test */
    public function it_can_handle_multiple_sagas()
    {
        $this->markTestIncomplete();
    }
}
