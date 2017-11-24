<?php

declare(strict_types=1);

class Config
{
    const AVAILABLE_SEATS = 'available_seats';
    const PRICE_PER_SEAT  = 'price_per_seat';

    public static function get(): array
    {
        return [
            self::AVAILABLE_SEATS => getenv(self::AVAILABLE_SEATS) ?: 10,
            self::PRICE_PER_SEAT  => getenv(self::PRICE_PER_SEAT) ?: 100,
        ];
    }
}
