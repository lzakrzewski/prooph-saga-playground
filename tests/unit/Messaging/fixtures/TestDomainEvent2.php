<?php

declare(strict_types=1);

namespace tests\unit\Messaging\fixtures;

use Messaging\DomainEvent;
use Messaging\ReturnsPayload;
use Ramsey\Uuid\UuidInterface;

class TestDomainEvent2 implements DomainEvent
{
    use ReturnsPayload;

    /** @var UuidInterface */
    public $messageId;

    /** @var int */
    private $value;

    public function __construct(UuidInterface $messageId, int $value)
    {
        $this->messageId = $messageId;
        $this->value     = $value;
    }

    public function aggregateId(): UuidInterface
    {
        return $this->messageId;
    }
}
