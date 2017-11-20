<?php

declare(strict_types=1);

namespace tests\acceptance;

use Application\Command\MakeReservation;
use Domain\Reservation\SeatsReserved;
use Ramsey\Uuid\Uuid;
use tests\UsesScenarioTestCase;

class MakeReservationTest extends UsesScenarioTestCase
{
    /** @test */
    public function it_notifies_that_message_has_been_created()
    {
        $this
            ->scenario
            ->when(new MakeReservation($reservationId = Uuid::uuid4(), 5))
            ->then(new SeatsReserved($reservationId, 5));
    }
}
