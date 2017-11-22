<?php

declare(strict_types=1);

namespace Messaging\Event;

use Messaging\DomainEvent;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

class SeatsAddedToWaitList implements DomainEvent
{
    use MessageWithPayload;

    /** @var UuidInterface */
    private $waitListId;

    /** @var int */
    private $numberOfSeats;

    public function __construct(UuidInterface $waitListId, int $numberOfSeats)
    {
        $this->waitListId    = $waitListId;
        $this->numberOfSeats = $numberOfSeats;
    }
}
