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

    /** @var \DateTime */
    private $markedAsDoneAt;

    private function __construct(UuidInterface $processId, array $payload, ?\DateTime $markedAsDoneAt)
    {
        $this->payload        = $payload;
        $this->processId      = $processId;
        $this->markedAsDoneAt = $markedAsDoneAt;
    }

    public static function start(UuidInterface $processId, array $payload): self
    {
        return new self($processId, $payload, null);
    }

    public function apply(array $payload): self
    {
        if ($this->markedAsDoneAt instanceof \DateTime) {
            throw new \DomainException('Can not modify state when its done');
        }

        return new self($this->processId, array_merge($this->payload, $payload), $this->markedAsDoneAt);
    }

    public function done(): self
    {
        return new self($this->processId, $this->payload, new \DateTime());
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
