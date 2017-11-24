<?php

declare(strict_types=1);

namespace Messaging;

use Ramsey\Uuid\UuidInterface;

interface Message
{
    public function aggregateId(): UuidInterface;

    public function payload(): array;
}
