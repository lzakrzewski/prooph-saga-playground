<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\Saga;

use Messaging\Command\MakeReservation;
use Messaging\Event\OrderCreated;
use Ramsey\Uuid\Uuid;
use tests\UsesScenarioTestCase;

class SagaTest extends UsesScenarioTestCase
{
    /** @test */
    public function it_makes_a_reservation_when_an_order_has_been_created()
    {
        $this->scenario()
            ->when(new OrderCreated(Uuid::uuid4(), 5))
            ->then(new MakeReservation($this->lastGeneratedAggregateId(), 5));
    }
}
