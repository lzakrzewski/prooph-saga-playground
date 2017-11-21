<?php

declare(strict_types=1);

namespace Application\Command;

use Domain\Reservation\SeatsNotReserved;
use Domain\Reservation\SeatsReserved;
use Prooph\ServiceBus\EventBus;

class MakeReservationHandler
{
    /** @var EventBus */
    private $eventBus;

    /** @var int */
    private $availableSeats;

    public function __construct(EventBus $eventBus, int $availableSeats)
    {
        $this->eventBus       = $eventBus;
        $this->availableSeats = $availableSeats;
    }

    public function __invoke(MakeReservation $command)
    {
        if ($command->numberOfSeats > $this->availableSeats) {
            $this->eventBus->dispatch(new SeatsNotReserved($command->reservationId, $command->numberOfSeats));

            return;
        }

        $this->eventBus->dispatch(new SeatsReserved($command->reservationId, $command->numberOfSeats));
    }
}
