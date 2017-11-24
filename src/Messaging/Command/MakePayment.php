<?php

declare(strict_types=1);

namespace Messaging\Command;

use Messaging\Command;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

class MakePayment implements Command
{
    use MessageWithPayload;

    /** @var UuidInterface */
    public $paymentId;

    /** @var int */
    public $numberOfSeats;

    public function __construct(UuidInterface $paymentId, int $numberOfSeats)
    {
        $this->paymentId     = $paymentId;
        $this->numberOfSeats = $numberOfSeats;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->paymentId;
    }
}
