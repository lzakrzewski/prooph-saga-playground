<?php

declare(strict_types=1);

namespace Messaging\Command;

use Messaging\Command;
use Messaging\ReturnsPayload;
use Ramsey\Uuid\UuidInterface;

class MakeReservation implements Command
{
    use ReturnsPayload;

    /** @var UuidInterface */
    public $reservationId;

    /** @var UuidInterface */
    public $orderId;

    /** @var int */
    public $numberOfSeats;

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
