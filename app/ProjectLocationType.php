<?php

namespace App;

enum ProjectLocationType: string 
{
    case NATIONWIDE = 'nationwide';
    case INTERREGIONAL ='inter-regional';
    case REGIONWIDE = 'regionwide';
    case INTERPROVINCE = 'inter-province';
    case PROVINCEWIDE = 'provincewide';
    case LOCATIONSPECIFIC = 'locationspecific';

    public function label():string 
    {
        return match ($this) {
            self::NATIONWIDE => 'Nationwide',
            self::INTERREGIONAL => 'Inter-Regional',
            self::REGIONWIDE => 'Regionwide',
            self::INTERPROVINCE => 'Inter-Province',
            self::PROVINCEWIDE => 'Provincewide',
            self::LOCATIONSPECIFIC => 'Location Specific',
        };
    }
}
