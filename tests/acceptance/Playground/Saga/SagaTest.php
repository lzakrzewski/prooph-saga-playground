<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\Saga;

use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Messaging\Event\OrderCreated;
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

    /**
     * @test
     */
    public function it_makes_a_payment_when_seats_has_been_reserved()
    {
        $this->scenario()
            ->given(new OrderCreated(Uuid::uuid4(), 5))
            ->when(new SeatsReserved($this->lastGeneratedAggregateIds()[1], 5, 5 * 100))
            ->then(new MakePayment($this->lastGeneratedAggregateIds()[2], 5));
    }
}
