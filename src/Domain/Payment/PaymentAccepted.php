<?php

declare(strict_types=1);

namespace Domain\Payment;

use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\UuidInterface;

class PaymentAccepted extends AggregateChanged
{
    public function __construct(UuidInterface $paymentId, int $amount)
    {
        parent::__construct($paymentId->toString(), ['amount' => $amount]);
    }

    public function numberOfSeats(): int
    {
        return $this->payload['numberOfSeats'];
    }
}
