<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use App\Models\Season;
use App\Models\SeasonRaceRanking;

class SeasonEnsureCurrent extends Command
{
    protected $signature = 'season:ensure-current';

    protected $description = 'Ensure current (and previous) season and ranking rows per race exist';

    public function handle(): int
    {
        $now = Date::now();
        $year = (int) $now->format('Y');
        $month = (int) $now->format('n');

        $this->info("Ensuring season for {$year}-{$month}");

        $season = Season::firstOrCreate([
            'year' => $year,
            'month' => $month,
        ], [
            'status' => 'active',
        ]);

        // ensure previous month
        $prev = $now->copy()->subMonth();
        $py = (int) $prev->format('Y');
        $pm = (int) $prev->format('n');

        $this->info("Ensuring previous season {$py}-{$pm} (closed)");
        $prevSeason = Season::firstOrCreate([
            'year' => $py,
            'month' => $pm,
        ], [
            'status' => 'closed',
        ]);

        $raceIds = DB::table('races')->pluck('id');

        foreach ([$season, $prevSeason] as $s) {
            foreach ($raceIds as $rid) {
                SeasonRaceRanking::firstOrCreate([
                    'season_id' => $s->id,
                    'race_id' => $rid,
                ], [
                    'points' => 0,
                ]);
            }
        }

        $this->info('Seasons and season_race_rankings ensured.');
        return Command::SUCCESS;
    }
}
