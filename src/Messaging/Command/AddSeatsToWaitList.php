<?php

declare(strict_types=1);

namespace Messaging\Command;

use Ramsey\Uuid\UuidInterface;

class AddSeatsToWaitList
{
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
