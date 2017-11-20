<?php

declare(strict_types=1);

namespace Application\Command;

use Prooph\ServiceBus\EventBus;

class MakePaymentHandler
{
    /** @var EventBus */
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus  = $eventBus;
    }

    public function __invoke(MakePayment $command)
    {
        $this->eventBus->dispatch('sth');
    }
}
