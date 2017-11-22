<?php

declare(strict_types=1);

namespace Playground\Command;

use Ramsey\Uuid\UuidInterface;

class MakePayment
{
    /** @var UuidInterface */
    public $paymentId;

    /** @var int */
    public $amount;

    public function __construct(UuidInterface $paymentId, $amount)
    {
        $this->paymentId = $paymentId;
        $this->amount    = $amount;
    }
}
