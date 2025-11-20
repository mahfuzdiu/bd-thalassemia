<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case COD = 'cod';
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
