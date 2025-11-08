<?php

namespace App\Enums;

enum PaymentMethodEnum : string
{
    case CashOnDelivery = 'Cash on Delivery';
    case OnlinePayment = 'Online Payment';
    case PickupPayment = 'Pickup Payment';

    public static function all(): array
    {
        return [
            self::CashOnDelivery->value,
            self::OnlinePayment->value,
            self::PickupPayment->value,
        ];
    }

    public static function pickupOrderTypeAvailable(): array
    {
        return [
            self::PickupPayment->value,
            self::OnlinePayment->value,
        ];
    }

    public static function deliveryOrderTypeAvailable(): array
    {
        return [
            self::CashOnDelivery->value,
            self::OnlinePayment->value,
        ];
    }
}
