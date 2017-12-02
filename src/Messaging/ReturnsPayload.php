<?php

declare(strict_types=1);

namespace Messaging;

trait ReturnsPayload
{
    public function payload(): array
    {
        return array_reduce(
            (new \ReflectionClass($message = $this))->getProperties(),
            function (array $payload, \ReflectionProperty $property) use ($message) {
                if (true === $property->isPrivate()) {
                    $property->setAccessible(true);
                }

                return array_merge(
                    $payload,
                    [
                        $property->getName() => (string) $property->getValue($message),
                    ]
                );
            },
            []
        );
    }
}
