<?php

namespace App\Enums;

enum DropoffPlace: string
{
    case ENTRANCE = '玄関';
    case GARAGE = '車庫';
    case BICYCLE = '自転車';
    case OTHER = 'その他';

    public static function getPlaceId()
    {
        return [
            self::ENTRANCE->value => 1,
            self::GARAGE->value => 2,
            self::BICYCLE->value => 3,
            self::OTHER->value => 4,
        ];
    }
}
