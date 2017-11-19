<?php

declare(strict_types=1);

namespace Domain\Order;

use Ramsey\Uuid\UuidInterface;

interface OrderRepository
{
    public function save(Order $order);

    public function get(UuidInterface $orderId): Order;
}
