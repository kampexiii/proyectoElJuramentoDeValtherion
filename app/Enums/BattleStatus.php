<?php

declare(strict_types=1);

namespace App\Enums;

enum BattleStatus: string
{
    case Active = 'active';
    case Finished = 'finished';
}
