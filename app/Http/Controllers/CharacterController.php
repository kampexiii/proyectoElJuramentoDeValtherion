<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Race;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        if ($user && $user->character) {
            return redirect()->route('game.personaje.edit');
        }

        $races = Race::orderBy('name')->get();

        return view('game.character.create', [
            'races' => $races,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user && $user->character) {
            return redirect()->route('game.personaje.edit');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'race_id' => ['nullable', 'exists:races,id'],
            'stats.fuerza' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.magia' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.defensa' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.velocidad' => ['nullable', 'integer', 'min:0', 'max:999'],
            'has_mount' => ['nullable', 'boolean'],
        ]);

        $stats = $this->normalizarStats($validated['stats'] ?? []);

        Character::create([
            'user_id' => $user->id,
            'race_id' => $validated['race_id'] ?? null,
            'name' => $validated['name'],
            'stats_json' => $stats,
            'has_mount' => $request->boolean('has_mount'),
        ]);

        return redirect()->route('game.personaje.edit');
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->character) {
            return redirect()->route('game.personaje.create');
        }

        $races = Race::orderBy('name')->get();

        return view('game.character.edit', [
            'character' => $user->character,
            'races' => $races,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->character) {
            return redirect()->route('game.personaje.create');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'race_id' => ['nullable', 'exists:races,id'],
            'stats.fuerza' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.magia' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.defensa' => ['nullable', 'integer', 'min:0', 'max:999'],
            'stats.velocidad' => ['nullable', 'integer', 'min:0', 'max:999'],
            'has_mount' => ['nullable', 'boolean'],
        ]);

        $stats = $this->normalizarStats($validated['stats'] ?? []);

        $user->character->update([
            'race_id' => $validated['race_id'] ?? null,
            'name' => $validated['name'],
            'stats_json' => $stats,
            'has_mount' => $request->boolean('has_mount'),
        ]);

        return redirect()->route('game.personaje.edit');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        if ($user && $user->character) {
            $user->character->delete();
        }

        return redirect()->route('home');
    }

    private function normalizarStats(array $stats): array
    {
        return [
            'fuerza' => $this->valorEntero($stats['fuerza'] ?? null),
            'magia' => $this->valorEntero($stats['magia'] ?? null),
            'defensa' => $this->valorEntero($stats['defensa'] ?? null),
            'velocidad' => $this->valorEntero($stats['velocidad'] ?? null),
        ];
    }

    private function valorEntero($valor): ?int
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return (int) $valor;
    }
}
