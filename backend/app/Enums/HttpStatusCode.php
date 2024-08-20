<?php

namespace App\Enums;

/**
 * Http Code
 */
enum HttpStatusCode: string
{
    case OK = '200';
    case CREATED = '201';
    case BAD_REQUEST = '400';
    case NOT_FOUND = '404';
    case UNPROCESSABLE_CONTENT = '422';
    case UNAUTHORIZED = '401';

    public static function toArray()
    {
        return array_column(self::cases(), 'value');
    }
}
