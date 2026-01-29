<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SeasonEnsureCurrent::class,
        \App\Console\Commands\SeasonCloseMonth::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        //
    }

    protected function commands(): void
    {
        //
    }
}
