<?php

namespace App\Enums;

/**
 * User Types
 */
enum UserTypes: string
{
    case client = "client";
    case admin = "admin";

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
