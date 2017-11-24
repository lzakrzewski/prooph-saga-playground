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
    public $amount;

    public function __construct(UuidInterface $paymentId, $amount)
    {
        $this->paymentId = $paymentId;
        $this->amount    = $amount;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->paymentId;
    }
}
