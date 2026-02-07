<?php

declare(strict_types=1);

namespace Tests\Feature\Missions;

use App\Models\FinalBoss;
use App\Models\Mission;
use App\Models\MissionChoice;
use App\Models\MissionNode;
use App\Models\MissionReward;
use App\Services\Missions\MissionGraphValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionGraphValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_validator_fails_when_missing_start_node(): void
    {
        $mission = $this->createMissionWithGraph(false, false);

        $errors = app(MissionGraphValidator::class)->validate($mission->load('reward'));

        $this->assertNotEmpty($errors);
        $this->assertTrue(collect($errors)->contains('Debe existir exactamente 1 nodo marcado como inicio.'));
    }

    public function test_validator_fails_when_choice_points_to_wrong_step(): void
    {
        $mission = $this->createMissionWithGraph(true, true);

        $errors = app(MissionGraphValidator::class)->validate($mission->load('reward'));

        $this->assertNotEmpty($errors);
        $this->assertTrue(collect($errors)->contains(function (string $error) {
            return str_contains($error, 'debe apuntar al paso 2');
        }));
    }

    private function createMissionWithGraph(bool $withStart, bool $brokenLink): Mission
    {
        $boss = FinalBoss::query()->create([
            'name' => 'Boss Test',
            'slug' => 'boss-test',
            'lore' => null,
            'base_stats_json' => ['hp' => 100, 'damage' => 10, 'defense' => 5],
        ]);

        $mission = Mission::query()->create([
            'title' => 'Mision Test',
            'slug' => 'mision-test-' . ($withStart ? 'start' : 'nostart') . ($brokenLink ? '-broken' : ''),
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
                'is_start' => $withStart && $step === 1,
                'title' => 'Paso ' . $step,
                'body_text' => 'Texto ' . $step,
            ]);
        }

        foreach (range(1, 5) as $step) {
            $nextStep = $step + 1;
            for ($i = 1; $i <= 3; $i++) {
                $targetStep = $nextStep;
                if ($brokenLink && $step === 1 && $i === 3) {
                    $targetStep = 3;
                }
                MissionChoice::query()->create([
                    'mission_node_id' => $nodes[$step]->id,
                    'choice_text' => 'Opcion ' . $i,
                    'outcome_text' => null,
                    'difficulty_points' => 1,
                    'effects_json' => null,
                    'next_node_id' => $nodes[$targetStep]->id,
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
