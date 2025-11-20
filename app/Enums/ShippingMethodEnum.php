<?php

namespace App\Enums;

enum ShippingMethodEnum: string
{
    case STANDARD = 'standard';
    case SUNDARBAN = 'sundarban';
    case FAST_COURIER = 'fast_courier';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
