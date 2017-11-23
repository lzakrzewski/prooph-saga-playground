<?php

declare(strict_types=1);

namespace tests\unit\Messaging\fixtures;

use Messaging\Command;
use Messaging\DomainEvent;
use Messaging\MessageWithPayload;
use Ramsey\Uuid\UuidInterface;

class TestMessage implements DomainEvent, Command
{
    use MessageWithPayload;

    /** @var UuidInterface */
    public $messageId;

    /** @var int */
    private $value;

    public function __construct(UuidInterface $messageId, int $value)
    {
        $this->messageId = $messageId;
        $this->value     = $value;
    }
}
