<?php

declare(strict_types=1);

namespace Messaging\ProcessManager;

use Ramsey\Uuid\UuidInterface;

class State
{
    /** @var UuidInterface */
    private $processId;

    /** @var array */
    private $payload = [];

    private function __construct(UuidInterface $processId, array $payload)
    {
        $this->payload    = $payload;
        $this->processId  = $processId;
    }

    public static function create(UuidInterface $processId, array $payload): self
    {
        return new self($processId, $payload);
    }

    public function apply(array $payload): self
    {
        return new self($this->processId, array_merge($this->payload, $payload));
    }

    public function has(string $key): bool
    {
        return isset($this->payload[$key]) && false === empty($this->payload[$key]);
    }

    public function processId(): UuidInterface
    {
        return $this->processId;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
