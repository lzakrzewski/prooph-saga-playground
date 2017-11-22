<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\Command;

use Messaging\Command\MakeReservation;
use Messaging\Event\SeatsNotReserved;
use Messaging\Event\SeatsReserved;
use Ramsey\Uuid\Uuid;
use tests\UsesScenarioTestCase;

class MakeReservationTest extends UsesScenarioTestCase
{
    /** @test */
    public function it_notifies_that_seats_have_been_reserved()
    {
        $this
            ->scenario
            ->when(new MakeReservation($reservationId = Uuid::uuid4(), 5))
            ->then(new SeatsReserved($reservationId, 5));
    }

    /** @test */
    public function it_notifies_that_seats_have_not_been_reserved()
    {
        $tooManySeats = $this->getFromContainer(\Config::AVAILABLE_SEATS) + 2;

        $this
            ->scenario
            ->when(new MakeReservation($reservationId = Uuid::uuid4(), $tooManySeats))
            ->then(new SeatsNotReserved($reservationId, $tooManySeats));
    }
}
