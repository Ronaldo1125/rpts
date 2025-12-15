<?php

namespace App;

enum ProjectLocation: string
{
    case REGIONWIDE = 'regionwide';
    case INTERPROVINCE = 'interprovince';
    case LOCATIONSPECIFIC = 'locationspecific';

    public function label():string 
    {
        return match ($this) {
            self::REGIONWIDE => 'Regionwide',
            self::INTERPROVINCE => 'Inter-Province',
            self::LOCATIONSPECIFIC => 'Location Specific',
        };
    }
}
