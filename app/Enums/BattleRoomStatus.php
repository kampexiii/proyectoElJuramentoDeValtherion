<?php

declare(strict_types=1);

namespace App\Enums;

enum BattleRoomStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Finished = 'finished';
    case Closed = 'closed';
}
