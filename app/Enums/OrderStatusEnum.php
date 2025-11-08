<?php

namespace App\Enums;

enum OrderStatusEnum : string
{
    case PENDING = 'Pending';
    case CONFIRMED = 'Confirmed';
    case PROCESSING = 'Processing';
    case SHIPPED = 'Shipped';
    case FOR_DELIVERY = 'For delivery';
    case DELIVERED = 'Delivered';
    case RELEASED = 'Released';
    case CANCELLED = 'Cancelled';
    case REFUNDED = 'Refunded';
    case REJECTED = 'Rejected';

    public static function all(): array
    {
        return [
            self::PENDING->value,
            self::CONFIRMED->value,
            self::PROCESSING->value,
            self::SHIPPED->value,
            self::FOR_DELIVERY->value,
            self::DELIVERED->value,
            self::RELEASED->value,
            self::CANCELLED->value,
            self::REFUNDED->value,
            self::REJECTED->value,
        ];
    }
}
