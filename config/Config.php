<?php

class Config
{
    const AVAILABLE_SEATS = 'available_seats';

    public static function parameters():array
    {
        return [
            self::AVAILABLE_SEATS => getenv(self::AVAILABLE_SEATS) ?: 10
        ];
    }
}
