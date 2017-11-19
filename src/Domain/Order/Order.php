<?php

declare(strict_types=1);

namespace Domain\Order;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Order extends AggregateRoot
{
    /** @var UuidInterface */
    private $orderId;

    /** @var int */
    private $numberOfSeats;

    public static function create(UuidInterface $orderId, int $numberOfSeats): self
    {
        $self = new self();
        $self->recordThat(new OrderCreated($orderId, $numberOfSeats));

        return$self;
    }

    protected function apply(AggregateChanged $event): void
    {
        if ($event instanceof OrderCreated) {
            $this->orderId       = Uuid::fromString($event->aggregateId());
            $this->numberOfSeats = $event->numberOfSeats();
        }
    }

    public function aggregateId(): string
    {
        return $this->orderId->toString();
    }

    public function numberOfSeats(): int
    {
        return $this->numberOfSeats;
    }
}
