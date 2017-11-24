<?php

declare(strict_types=1);

namespace Messaging\Command\Handler;

use Messaging\Command\MakePayment;
use Messaging\Event\PaymentAccepted;
use Prooph\ServiceBus\EventBus;

class MakePaymentHandler
{
    /** @var EventBus */
    private $eventBus;

    /** @var int */
    private $pricePerSeat;

    public function __construct(EventBus $eventBus, int $pricePerSeat)
    {
        $this->eventBus     = $eventBus;
        $this->pricePerSeat = $pricePerSeat;
    }

    public function __invoke(MakePayment $command)
    {
        $this->eventBus->dispatch(new PaymentAccepted($command->paymentId, $command->numberOfSeats * $this->pricePerSeat));
    }
}
