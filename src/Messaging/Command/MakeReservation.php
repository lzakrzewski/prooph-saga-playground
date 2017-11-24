<?php

declare(strict_types=1);

namespace Messaging\Command;

use Messaging\Command;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

class MakeReservation implements Command
{
    use MessageWithPayload;

    /** @var UuidInterface */
    public $reservationId;

    /** @var int */
    public $numberOfSeats;

    public function __construct(UuidInterface $reservationId, int $numberOfSeats)
    {
        $this->reservationId       = $reservationId;
        $this->numberOfSeats       = $numberOfSeats;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->reservationId;
    }
}
