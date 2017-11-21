<?php

declare(strict_types=1);

namespace tests\acceptance;

use Application\Command\AddSeatsToWaitList;
use Domain\WaitList\SeatsAddedToWaitList;
use Ramsey\Uuid\Uuid;
use tests\UsesScenarioTestCase;

class AddSeatsToWaitListTest extends UsesScenarioTestCase
{
    /** @test */
    public function it_notifies_that_seats_have_been_added_to_wait_list()
    {
        $this
            ->scenario
            ->when(new AddSeatsToWaitList($reservationId = Uuid::uuid4(), 5))
            ->then(new SeatsAddedToWaitList($reservationId, 5));
    }
}
