<?php

declare(strict_types=1);

namespace tests\integration\Persistence;

use Domain\Order\Order;
use Domain\Order\OrderRepository;
use Infrastructure\Persistence\EventSourcedOrderRepository;
use Ramsey\Uuid\Uuid;
use tests\UsesContainerTestCase;

class EventSourcedOrderRepositoryTest extends UsesContainerTestCase
{
    /** @var EventSourcedOrderRepository */
    private $repository;

    /** @test */
    public function it_can_get_order_by_id()
    {
        $this->repository->save(Order::create($orderId = Uuid::uuid4(), 5));

        $order = $this->repository->get($orderId);

        $this->assertInstanceOf(Order::class, $this->repository->get($orderId));
        $this->assertTrue($orderId->equals(Uuid::fromString($order->aggregateId())));
        $this->assertEquals(5, $order->numberOfSeats());
    }

    /** @test */
    public function it_fails_when_order_does_not_exist()
    {
        $this->expectException(\DomainException::class);

        $this->repository->get(Uuid::uuid4());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->getService(OrderRepository::class);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->repository = null;
    }
}
