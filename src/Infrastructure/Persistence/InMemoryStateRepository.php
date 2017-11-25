<?php

declare(strict_types=1);

namespace Infrastructure\Persistence;

use Messaging\Saga\State;
use Messaging\Saga\StateRepository;
use Ramsey\Uuid\UuidInterface;

class InMemoryStateRepository implements StateRepository
{
    /** @var State[] */
    private static $states = [];

    public function find(UuidInterface $sagaId): ?State
    {
        if (false === $this->hasState($sagaId)) {
            return null;
        }

        return self::$states[$sagaId->toString()];
    }

    public function save(State $state)
    {
        self::$states[$state->sagaId()->toString()] = $state;
    }

    //Todo: remove this
    public function lastState(): ?State
    {
        $states = self::$states;

        if (empty($states)) {
            return null;
        }

        return end($states);
    }

    public function reset()
    {
        self::$states = [];
    }

    private function hasState(UuidInterface $sagaId): bool
    {
        return isset(self::$states[$sagaId->toString()]);
    }
}
