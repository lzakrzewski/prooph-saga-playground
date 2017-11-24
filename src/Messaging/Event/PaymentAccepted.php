<?php

declare(strict_types=1);

namespace Messaging\Event;

use Messaging\DomainEvent;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

class PaymentAccepted implements DomainEvent
{
    use MessageWithPayload;

    /** @var UuidInterface */
    private $paymentId;

    /** @var int */
    private $amount;

    public function __construct(UuidInterface $paymentId, int $amount)
    {
        $this->paymentId = $paymentId;
        $this->amount    = $amount;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->paymentId;
    }
}
