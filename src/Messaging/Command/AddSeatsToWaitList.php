<?php

declare(strict_types=1);

namespace Messaging\Command;

use Messaging\Command;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

class AddSeatsToWaitList implements Command
{
    use MessageWithPayload;

    /** @var UuidInterface */
    public $waitListId;

    /** @var int */
    public $numberOfSeats;

    public function __construct(UuidInterface $waitListId, $numberOfSeats)
    {
        $this->waitListId    = $waitListId;
        $this->numberOfSeats = $numberOfSeats;
    }
}
