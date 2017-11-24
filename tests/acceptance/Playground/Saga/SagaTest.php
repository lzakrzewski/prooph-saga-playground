<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\Saga;

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
    public function it_makes_a_reservation_when_an_order_has_been_created()
    {
        $this->scenario()
            ->when(new OrderCreated(Uuid::uuid4(), 5))
            ->then(new MakeReservation($this->lastGeneratedAggregateIds()[1], 5));
    }

    /** @test */
    public function it_makes_a_payment_when_seats_has_been_reserved()
    {
        $this->scenario()
            ->given(new OrderCreated(Uuid::uuid4(), 5))
            ->when(new SeatsReserved($this->lastGeneratedAggregateIds()[1], 5, 5 * 100))
            ->then(new MakePayment($this->lastGeneratedAggregateIds()[2], 5));
    }

    /** @test */
    public function it_confirms_order_when_payment_has_been_accepted()
    {
        $this->scenario()
            ->given(
                new OrderCreated($orderId = Uuid::uuid4(), 5),
                new SeatsReserved(Uuid::uuid4(), 5, 500)
            )
            ->when(new PaymentAccepted($this->lastGeneratedAggregateIds()[2], 5, 5 * 100))
            ->then(new OrderConfirmed($orderId, 5));
    }

    /** @test */
    public function it_does_not_confirm_order_when_seats_has_not_been_reserved()
    {
        $this->markTestIncomplete();

        $this->scenario()
            ->given(
                new OrderCreated($orderId = Uuid::uuid4(), 5),
                new SeatsNotReserved(Uuid::uuid4(), 5, 500)
            )
            ->when(new PaymentAccepted($this->lastGeneratedAggregateIds()[2], 5, 5 * 100))
            ->thenNot(new OrderConfirmed($orderId = Uuid::uuid4(), 5));
    }

    /** @test */
    public function it_can_handle_multiple_sagas()
    {
        $this->markTestIncomplete();
    }
}
