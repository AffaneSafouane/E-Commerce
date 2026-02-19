<?php

namespace App\Enum;

enum AddressType: string
{
    case HOME = 'home';
    case BILLING = 'billing';

    public static function getTypes(): array
    {
        return [
            self::HOME,
            self::BILLING,
        ];
    }
}
