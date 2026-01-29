<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use App\Models\Season;
use App\Models\SeasonRaceRanking;
use App\Models\SeasonRaceWinner;

class SeasonCloseMonth extends Command
{
    protected $signature = 'season:close {year} {month}';

    protected $description = 'Close a season (year month) and record the race winner';

    public function handle(): int
    {
        $year = (int) $this->argument('year');
        $month = (int) $this->argument('month');

        $season = Season::where('year', $year)->where('month', $month)->first();
        if (!$season) {
            $this->error('Season not found.');
            return Command::FAILURE;
        }

        $season->status = 'closed';
        $season->closed_at = Date::now();
        $season->save();

        $top = SeasonRaceRanking::where('season_id', $season->id)
            ->orderByDesc('points')
            ->orderBy('race_id')
            ->first();

        if (!$top) {
            $this->info('No rankings available for this season. No winner created.');
            return Command::SUCCESS;
        }

        SeasonRaceWinner::updateOrCreate([
            'season_id' => $season->id,
        ], [
            'race_id' => $top->race_id,
            'points' => $top->points,
            'awarded_at' => Date::now(),
        ]);

        $this->info('Winner recorded.');
        return Command::SUCCESS;
    }
}
