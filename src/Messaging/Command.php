<?php

declare(strict_types=1);

namespace Messaging;

interface Command
{
    public function payload(): array;
}
