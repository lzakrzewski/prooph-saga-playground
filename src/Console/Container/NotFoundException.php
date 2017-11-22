<?php

declare(strict_types=1);

namespace Console\Container;

use Psr\Container\NotFoundExceptionInterface;

final class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
    public static function with($id): self
    {
        return new self(
            sprintf(
                'Container does not have service nor parameter with id "%s".',
                $id
            )
        );
    }
}
