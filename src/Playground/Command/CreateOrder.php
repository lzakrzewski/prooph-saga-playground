<?php

declare(strict_types=1);

namespace Playground\Command;

use Ramsey\Uuid\UuidInterface;

class CreateOrder
{
    /** @var UuidInterface */
    public $orderId;

    /** @var int */
    public $numberOfSeats;

    public function __construct(UuidInterface $orderId, int $numberOfSeats)
    {
        $this->orderId       = $orderId;
        $this->numberOfSeats = $numberOfSeats;
    }
}
