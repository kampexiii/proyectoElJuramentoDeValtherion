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

                $level = '—';
                if (Schema::hasColumn('characters', 'level')) {
                    $level = $character->level ?? '—';
                }

                $characterSummary = [
                    'name' => $character->name,
                    'race' => $character->race->name ?? '—',
                    'level' => $level,
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
            ->when($user, fn ($query) => $query->where('id', '!=', $user->id))
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
