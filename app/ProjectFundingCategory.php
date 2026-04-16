<?php

namespace App;

enum ProjectFundingCategory: string
{
    case REGULAR = 'tier1';
    case LOCALLY = 'tier2';
    case MULTIYEAR = 'multi-year';

    public function label(): string
    {
        return match($this){
            self::REGULAR => 'Tier 1',
            self::LOCALLY => 'Tier 2',
             self::MULTIYEAR => 'Multi-Year Allocation',
        };
    }
}
