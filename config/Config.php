<?php

declare(strict_types=1);

class Config
{
    const AVAILABLE_SEATS = 'PLAYGROUND_AVAILABLE_SEATS';
    const PRICE_PER_SEAT  = 'PLAYGROUND_PRICE_PER_SEAT';

    public static function get(): array
    {
        return [
            self::AVAILABLE_SEATS => (int) getenv(self::AVAILABLE_SEATS) ?: 10,
            self::PRICE_PER_SEAT  => (int) getenv(self::PRICE_PER_SEAT) ?: 100,
        ];
    }
}
