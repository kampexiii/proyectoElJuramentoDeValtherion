<?php

declare(strict_types=1);

namespace App\Enums;

enum MissionRunStatus: string
{
    case Active = 'active';
    case BossPending = 'boss_pending';
    case Completed = 'completed';
    case Abandoned = 'abandoned';
    case Failed = 'failed';
}
