<?php

declare(strict_types=1);

namespace Messaging\Command\Handler;

use Messaging\Command\MakeReservation;
use Messaging\Event\SeatsNotReserved;
use Messaging\Event\SeatsReserved;
use Prooph\ServiceBus\EventBus;

class MakeReservationHandler
{
    /** @var EventBus */
    private $eventBus;

    /** @var int */
    private $availableSeats;

    /** @var int */
    private $pricePerSeat;

    public function __construct(EventBus $eventBus, int $availableSeats, int $pricePerSeat)
    {
        $this->eventBus       = $eventBus;
        $this->availableSeats = $availableSeats;
        $this->pricePerSeat   = $pricePerSeat;
    }

    public function __invoke(MakeReservation $command)
    {
        if ($command->numberOfSeats > $this->availableSeats) {
            $this->eventBus->dispatch(new SeatsNotReserved($command->reservationId, $command->numberOfSeats));

            return;
        }

        $this->eventBus->dispatch(
            new SeatsReserved($command->reservationId, $numberOfSeats = $command->numberOfSeats, $numberOfSeats * $this->pricePerSeat)
        );
    }
}
