<?php

namespace App\Enums;

enum DropoffPlace: string
{
    case ENTRANCE = '玄関';
    case GAS_METER = 'ガスメーター';
    case GARAGE = '車庫';
    case BICYCLE = '自転車';
    case OTHER = 'その他';

    public static function getPlaceId()
    {
        return [
            self::ENTRANCE->value => 1,
            self::GAS_METER->value => 2,
            self::GARAGE->value => 3,
            self::BICYCLE->value => 4,
            self::OTHER->value => 5,
        ];
    }
}
