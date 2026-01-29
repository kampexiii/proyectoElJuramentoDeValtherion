<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class GameProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $characterSummary = null;

        if ($user && Schema::hasTable('characters')) {
            $character = Character::where('user_id', $user->id)->first();
            if ($character) {
                if (Schema::hasTable('races')) {
                    $character->load('race');
                }

                $gold = 0;
                if (Schema::hasColumn('characters', 'gold')) {
                    $gold = (int) ($character->gold ?? 0);
                }

                $xp = 0;
                if (Schema::hasColumn('characters', 'xp')) {
                    $xp = (int) ($character->xp ?? 0);
                }

                $level = 1;
                if (Schema::hasColumn('characters', 'level')) {
                    $level = (int) ($character->level ?? 1);
                }

                $hp_max = 100;
                if (Schema::hasColumn('characters', 'hp_max')) {
                    $hp_max = (int) ($character->hp_max ?? 100);
                }

                $hp_current = 100;
                if (Schema::hasColumn('characters', 'hp_current')) {
                    $hp_current = (int) ($character->hp_current ?? 100);
                }

                $stats = [];
                if (Schema::hasColumn('characters', 'stats_json')) {
                    $stats = $character->stats_json ?? [];
                }

                $characterSummary = [
                    'name' => $character->name,
                    'race' => $character->race->name ?? '—',
                    'gold' => $gold,
                    'xp' => $xp,
                    'level' => $level,
                    'hp_max' => $hp_max,
                    'hp_current' => $hp_current,
                    'stats' => $stats,
                ];
            }
        }

        return view('game.perfil', [
            'user' => $user,
            'characterSummary' => $characterSummary,
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
        ]);
    }

    public function availability(Request $request)
    {
        $data = $request->validate([
            'field' => ['required', Rule::in(['name', 'email'])],
            'value' => ['required', 'string', 'max:255'],
        ]);

        $field = $data['field'];
        $value = trim($data['value']);
        $user = $request->user();

        if ($field === 'name') {
            if (strlen($value) > 24) {
                return response()->json([
                    'available' => false,
                    'message' => 'El nombre es demasiado largo.',
                ]);
            }

            if (!preg_match('/^[A-Za-z0-9_-]+$/', $value)) {
                return response()->json([
                    'available' => false,
                    'message' => 'Formato no válido.',
                ]);
            }
        }

        $exists = User::query()
            ->where($field, $value)
            ->when($user, fn($query) => $query->where('id', '!=', $user->id))
            ->exists();

        if ($exists) {
            return response()->json([
                'available' => false,
                'message' => $field === 'email'
                    ? 'Ese correo ya está en uso.'
                    : 'Ese nombre de usuario ya está en uso.',
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Disponible',
        ]);
    }
}
