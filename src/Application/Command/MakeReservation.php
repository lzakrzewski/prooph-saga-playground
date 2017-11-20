<?php

declare(strict_types=1);

namespace Application\Command;

use Ramsey\Uuid\UuidInterface;

class MakeReservation
{
    /** @var UuidInterface */
    public $reservationId;

    /** @var int */
    public $numberOfSeats;

    public function __construct(UuidInterface $reservationId, int $numberOfSeats)
    {
        $this->reservationId       = $reservationId;
        $this->numberOfSeats       = $numberOfSeats;
    }
}
