<?php

declare(strict_types=1);

namespace Infrastructure\Persistence;

use Domain\Order\Order;
use Domain\Order\OrderRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\EventStore;
use Ramsey\Uuid\UuidInterface;

class EventSourcedOrderRepository extends AggregateRepository implements OrderRepository
{
    public function __construct(EventStore $eventStore)
    {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(Order::class),
            new AggregateTranslator(),
            null,
            null,
            true
        );
    }

    public function save(Order $order): void
    {
        $this->saveAggregateRoot($order);
    }

    public function get(UuidInterface $orderId): Order
    {
        $order = $this->getAggregateRoot($orderId->toString());

        if (null === $order) {
            throw new \DomainException(sprintf('Order with id "%s" does not exist.', $orderId));
        }

        return $order;
    }
}
