<?php

declare(strict_types=1);

namespace Infrastructure\Persistence;

use Messaging\ProcessManager\State;
use Messaging\ProcessManager\StateRepository;
use Ramsey\Uuid\UuidInterface;

class InMemoryStateRepository implements StateRepository
{
    /** @var State[] */
    private static $states = [];

    public function find(UuidInterface $processId): ?State
    {
        if (false === $this->hasState($processId)) {
            return null;
        }

        return self::$states[$processId->toString()];
    }

    public function save(State $state): void
    {
        self::$states[$state->processId()->toString()] = $state;
    }

    public function reset(): void
    {
        self::$states = [];
    }

    private function hasState(UuidInterface $processId): bool
    {
        return isset(self::$states[$processId->toString()]);
    }
}
