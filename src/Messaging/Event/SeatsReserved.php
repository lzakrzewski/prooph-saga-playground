<?php

declare(strict_types=1);

namespace Messaging\Event;

use Messaging\DomainEvent;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

final class SeatsReserved implements DomainEvent
{
    use MessageWithPayload;

    /** @var UuidInterface */
    private $reservationId;

    /** @var int */
    private $numberOfSeats;

    public function __construct(UuidInterface $reservationId, int $numberOfSeats)
    {
        $this->reservationId = $reservationId;
        $this->numberOfSeats = $numberOfSeats;
    }
}
