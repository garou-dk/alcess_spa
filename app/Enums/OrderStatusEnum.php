<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PROCESSING = 'Processing';
    case CONFIRMED = 'Confirmed';
    case SHIPPED = 'Shipped';
    case COMPLETED = 'Completed';
    case CANCELLED = 'Cancelled';

    public static function all(): array
    {
        return [
            self::PROCESSING->value,
            self::CONFIRMED->value,
            self::SHIPPED->value,
            self::COMPLETED->value,
            self::CANCELLED->value,
        ];
    }

    public function canTransitionTo(OrderStatusEnum $to): bool
    {
        $allowedTransitions = [
            self::PROCESSING->value => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED->value => [self::SHIPPED],
            self::SHIPPED->value => [self::COMPLETED],
            self::COMPLETED->value => [],
            self::CANCELLED->value => [],
        ];

        return in_array($to, $allowedTransitions[$this->value] ?? []);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PROCESSING => 'Processing',
            self::CONFIRMED => 'Confirmed',
            self::SHIPPED => 'Shipped',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::PROCESSING => 'bg-blue-50 text-blue-700',
            self::CONFIRMED => 'bg-green-50 text-green-700',
            self::SHIPPED => 'bg-purple-50 text-purple-700',
            self::COMPLETED => 'bg-gray-50 text-gray-700',
            self::CANCELLED => 'bg-red-50 text-red-700',
        };
    }
}
