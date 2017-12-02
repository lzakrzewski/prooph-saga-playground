<?php

declare(strict_types=1);

namespace Messaging\Event;

use Messaging\DomainEvent;
use Messaging\ReturnsPayload;
use Ramsey\Uuid\UuidInterface;

class OrderConfirmed implements DomainEvent
{
    use ReturnsPayload;

    /** @var UuidInterface */
    private $orderId;

    /** @var int */
    private $numberOfSeats;

    public function __construct(UuidInterface $orderId, int $numberOfSeats)
    {
        $this->orderId       = $orderId;
        $this->numberOfSeats = $numberOfSeats;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->orderId;
    }
}
