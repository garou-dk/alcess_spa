<?php

namespace App\Enums;

enum OrderTypeEnum : string
{
    case PICKUP = 'Pickup';

    case DELIVERY = 'Delivery';

    public static function all(): array
    {
        return [
            self::PICKUP->value,
            self::DELIVERY->value,
        ];
    }
}
