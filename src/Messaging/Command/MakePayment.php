<?php

declare(strict_types=1);

namespace Messaging\Command;

use Messaging\Command;
use Messaging\ReturnsPayload;
use Ramsey\Uuid\UuidInterface;

class MakePayment implements Command
{
    use ReturnsPayload;

    /** @var UuidInterface */
    public $paymentId;

    /** @var UuidInterface */
    public $orderId;

    /** @var int */
    public $amount;

    public function __construct(UuidInterface $paymentId, UuidInterface $orderId, int $amount)
    {
        $this->paymentId = $paymentId;
        $this->orderId   = $orderId;
        $this->amount    = $amount;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->paymentId;
    }
}
