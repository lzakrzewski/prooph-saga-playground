<?php

declare(strict_types=1);

namespace Messaging\Saga;

use Ramsey\Uuid\UuidInterface;

interface StateRepository
{
    public function find(UuidInterface $sagaId): ?State;

    public function save(State $state);

    //Todo: temp hack
    public function lastState(): ?State;
}
