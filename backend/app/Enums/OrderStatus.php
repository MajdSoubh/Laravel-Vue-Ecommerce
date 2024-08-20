<?php

namespace App\Enums;

/**
 * User Types
 */
enum OrderStatus: string
{
    case Unpaid = 'unpaid';
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Paid = 'paid';
    case Completed = 'completed';
    public static function toArray()
    {
        return array_column(self::cases(), 'value');
    }
}
