<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\Command;

use Messaging\Command\PlaceOrder;
use Messaging\Event\OrderPlaced;
use Ramsey\Uuid\Uuid;
use tests\UsesScenarioTestCase;

class PlaceOrderTest extends UsesScenarioTestCase
{
    /** @test */
    public function it_notifies_that_order_has_been_placed(): void
    {
        $this
            ->scenario()
            ->when(new PlaceOrder($orderId = Uuid::uuid4(), 5))
            ->then(new OrderPlaced($orderId, 5));
    }
}
