<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'Admin';
    case STAFF = 'Staff';
    case CUSTOMER = 'Customer';

    public static function values(): array
    {
        return [
            self::ADMIN->value,
            self::STAFF->value,
            self::CUSTOMER->value,
        ];
    }

    public static function storeUsers(): array
    {
        return [
            self::ADMIN->value,
            self::STAFF->value,
        ];
    }
}
