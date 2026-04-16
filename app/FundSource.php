<?php

namespace App;

enum FundSource: string
{
    case GAA = 'gaa';
    case PPP = 'ppp';
    case ODA = 'oda';
    case OTHERS = 'others';

    public function label():string
    {
        return match ($this) {
            self::GAA => 'GAA',
            self::PPP => 'PPP',
            self::ODA => 'ODA',
            self::OTHERS => 'Others',
        };
    }
}
