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
                if ($property->isPrivate()) {
                    return $payload;
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
