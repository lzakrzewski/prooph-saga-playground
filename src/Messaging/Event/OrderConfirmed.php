<?php

declare(strict_types=1);

namespace Messaging\Event;

use Messaging\DomainEvent;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

class OrderConfirmed implements DomainEvent
{
    use MessageWithPayload;

    /** @var UuidInterface */
    private $paymentId;

    /** @var int */
    private $numberOfSeats;

    public function __construct(UuidInterface $paymentId, int $numberOfSeats)
    {
        $this->paymentId     = $paymentId;
        $this->numberOfSeats = $numberOfSeats;
    }
}
