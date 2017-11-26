<?php

declare(strict_types=1);

namespace Messaging\Event;

use Messaging\DomainEvent;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

class SeatsNotReserved implements DomainEvent
{
    use MessageWithPayload;

    /** @var UuidInterface */
    private $reservationId;

    /** @var UuidInterface */
    private $orderId;

    /** @var int */
    private $numberOfSeats;

    public function __construct(UuidInterface $reservationId, UuidInterface $orderId, int $numberOfSeats)
    {
        $this->reservationId = $reservationId;
        $this->orderId       = $orderId;
        $this->numberOfSeats = $numberOfSeats;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->reservationId;
    }
}
