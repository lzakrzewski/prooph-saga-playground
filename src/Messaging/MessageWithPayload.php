<?php

declare(strict_types=1);

namespace Messaging;

trait MessageWithPayload
{
    public function payload(): array
    {
        return array_reduce(
            (new \ReflectionClass($message = $this))->getProperties(),
            function (array $payload, \ReflectionProperty $property) use ($message) {
                if (true === $property->isPrivate()) {
                    $property->setAccessible(true);
                }

                $value = (string) $property->getValue($message);

                if (true === $property->isPrivate()) {
                    $property->setAccessible(false);
                }

                return array_merge(
                    $payload,
                    [
                        $property->getName() => $value,
                    ]
                );
            },
            []
        );
    }
}
