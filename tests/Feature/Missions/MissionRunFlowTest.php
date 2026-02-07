<?php

declare(strict_types=1);

namespace Tests\Feature\Missions;

use App\Enums\MissionRunStatus;
use App\Models\Character;
use App\Models\FinalBoss;
use App\Models\Mission;
use App\Models\MissionChoice;
use App\Models\MissionNode;
use App\Models\MissionReward;
use App\Models\User;
use App\Services\Missions\MissionRunService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionRunFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_flow_reaches_boss_pending_after_six_choices(): void
    {
        $user = User::factory()->create();
        $character = Character::query()->create([
            'user_id' => $user->id,
            'race_id' => null,
            'name' => 'Test',
            'stats_json' => ['hp' => 100, 'strength' => 10, 'magic' => 10, 'defense' => 5, 'speed' => 5],
            'has_mount' => false,
        ]);

        $mission = $this->createMissionGraph();

        $service = app(MissionRunService::class);
        $run = $service->start($mission, $character);

        for ($step = 1; $step <= 6; $step++) {
            $node = MissionNode::query()->findOrFail($run->current_node_id);
            $choice = MissionChoice::query()->where('mission_node_id', $node->id)->orderBy('order')->firstOrFail();
            $run = $service->choose($run, $choice);
        }

        $run->refresh();

        $this->assertSame(MissionRunStatus::BossPending, $run->status);
        $this->assertNull($run->current_node_id);
    }

    private function createMissionGraph(): Mission
    {
        $boss = FinalBoss::query()->create([
            'name' => 'Boss Run',
            'slug' => 'boss-run',
            'lore' => null,
            'base_stats_json' => ['hp' => 100, 'damage' => 10, 'defense' => 5],
        ]);

        $mission = Mission::query()->create([
            'title' => 'Run Test',
            'slug' => 'run-test',
            'intro_text' => 'Intro',
            'context_text' => null,
            'status' => 'published',
            'repeatable' => false,
            'base_race_points' => 10,
            'final_boss_id' => $boss->id,
        ]);

        MissionReward::query()->create([
            'mission_id' => $mission->id,
            'xp' => 0,
            'gold' => 0,
            'items_json' => null,
        ]);

        $nodes = [];
        foreach (range(1, 6) as $step) {
            $nodes[$step] = MissionNode::query()->create([
                'mission_id' => $mission->id,
                'step_index' => $step,
                'is_start' => $step === 1,
                'title' => 'Paso ' . $step,
                'body_text' => 'Texto ' . $step,
            ]);
        }

        foreach (range(1, 5) as $step) {
            for ($i = 1; $i <= 3; $i++) {
                MissionChoice::query()->create([
                    'mission_node_id' => $nodes[$step]->id,
                    'choice_text' => 'Opcion ' . $i,
                    'outcome_text' => null,
                    'difficulty_points' => 1,
                    'effects_json' => null,
                    'next_node_id' => $nodes[$step + 1]->id,
                    'goes_to_boss' => false,
                    'order' => $i,
                ]);
            }
        }

        for ($i = 1; $i <= 3; $i++) {
            MissionChoice::query()->create([
                'mission_node_id' => $nodes[6]->id,
                'choice_text' => 'Boss ' . $i,
                'outcome_text' => null,
                'difficulty_points' => 1,
                'effects_json' => null,
                'next_node_id' => null,
                'goes_to_boss' => true,
                'order' => $i,
            ]);
        }

        return $mission;
    }
}
