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

    /** @var UuidInterface */
    private $orderId;

    /** @var int */
    private $numberOfSeats;

    /** @var int */
    private $reservationAmount;

    public function __construct(
        UuidInterface $reservationId,
        UuidInterface $orderId,
        int $numberOfSeats,
        int $reservationAmount
    ) {
        $this->reservationId     = $reservationId;
        $this->orderId           = $orderId;
        $this->numberOfSeats     = $numberOfSeats;
        $this->reservationAmount = $reservationAmount;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->reservationId;
    }
}
