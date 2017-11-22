<?php

declare(strict_types=1);

namespace Playground\Event;

use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\UuidInterface;

class SeatsAddedToWaitList extends AggregateChanged
{
    public function __construct(UuidInterface $waitListId, int $numberOfSeats)
    {
        parent::__construct($waitListId->toString(), ['numberOfSeats' => $numberOfSeats]);
    }

    public function numberOfSeats(): int
    {
        return $this->payload['numberOfSeats'];
    }
}
