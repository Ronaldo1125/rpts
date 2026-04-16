<?php

namespace App;

enum ProjectStatus: string
{
    case ONGOING = 'ongoing';
    case PROPOSED = 'proposed';
    case TERMINATED = 'terminated';
    case SUSPENDED = 'suspended';
    case DROPPED = 'dropped';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::ONGOING => 'Ongoing',
            self::PROPOSED => 'Proposed',
            self::TERMINATED => 'Terminated',
            self::SUSPENDED => 'Suspended',
            self::DROPPED => 'Dropped',
            self::COMPLETED => 'Completed',
        };
    }

    
}
