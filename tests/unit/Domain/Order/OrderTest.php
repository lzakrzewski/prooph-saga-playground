<?php

declare(strict_types=1);

namespace tests\unit\Domain\Order;

use Domain\Order\Order;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class OrderTest extends TestCase
{
    /** @test */
    public function it_can_be_created()
    {
        $order = Order::create($orderId = Uuid::uuid4(), 5);

        $this->assertEquals($orderId->toString(), $order->aggregateId());
        $this->assertEquals(5, $order->numberOfSeats());
    }
}
