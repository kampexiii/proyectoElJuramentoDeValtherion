<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Character;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BaselineAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_returns_200(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_private_game_route_redirects_to_login_when_guest(): void
    {
        $response = $this->get('/game/tienda');

        $response->assertRedirect('/login');
    }

    public function test_login_allows_access_to_game_home(): void
    {
        $user = User::factory()->create();

        $this->get('/dashboard');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'accept_cookies' => 'on',
            'accept_terms' => 'on',
        ]);

        $response->assertRedirect('/dashboard');

        $this->get('/home')->assertOk();
    }

    public function test_admin_route_is_forbidden_without_admin_role(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_route_allows_admin_role(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
    }

    public function test_user_cannot_create_more_than_one_character(): void
    {
        $user = User::factory()->create();

        Character::query()->create([
            'user_id' => $user->id,
            'race_id' => null,
            'name' => 'Primer personaje',
            'stats_json' => null,
            'has_mount' => false,
        ]);

        $response = $this->actingAs($user)->post('/game/personaje', [
            'name' => 'Segundo personaje',
            'race_id' => null,
        ]);

        $response->assertRedirect(route('game.personaje.edit'));
        $this->assertSame(1, Character::query()->where('user_id', $user->id)->count());
    }

    public function test_missions_index_shows_only_published_missions(): void
    {
        $user = User::factory()->create();

        Character::query()->create([
            'user_id' => $user->id,
            'race_id' => null,
            'name' => 'Hero',
            'stats_json' => null,
            'has_mount' => false,
        ]);

        Mission::query()->create([
            'slug' => 'mission-publica',
            'title' => 'Mision Publica',
            'intro_text' => 'Intro',
            'context_text' => null,
            'status' => 'published',
            'repeatable' => false,
            'base_race_points' => 0,
            'final_boss_id' => null,
        ]);

        Mission::query()->create([
            'slug' => 'mission-borrador',
            'title' => 'Mision Borrador',
            'intro_text' => 'Intro',
            'context_text' => null,
            'status' => 'draft',
            'repeatable' => false,
            'base_race_points' => 0,
            'final_boss_id' => null,
        ]);

        $response = $this->actingAs($user)->get('/game/misiones');

        $response->assertOk();
        $response->assertSee('Mision Publica');
        $response->assertDontSee('Mision Borrador');
    }
}
