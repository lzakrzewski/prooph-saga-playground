<?php

declare(strict_types=1);

namespace Messaging\Saga;

use Ramsey\Uuid\UuidInterface;

class State
{
    /** @var UuidInterface */
    private $sagaId;

    /** @var array */
    private $payload = [];

    private function __construct(UuidInterface $sagaId, array $payload)
    {
        $this->payload = $payload;
        $this->sagaId  = $sagaId;
    }

    public static function create(UuidInterface $sagaId, array $payload): self
    {
        return new self($sagaId, $payload);
    }

    public function apply(array $payload): self
    {
        return new self($this->sagaId, array_merge($this->payload, $payload));
    }

    public function sagaId(): UuidInterface
    {
        return $this->sagaId;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
