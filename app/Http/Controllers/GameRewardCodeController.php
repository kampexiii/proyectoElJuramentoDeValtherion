<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterItem;
use App\Models\Item;
use App\Models\Mount;
use App\Models\RewardCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GameRewardCodeController extends Controller
{
    public function redeem(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64'],
        ]);

        $user = $request->user();
        if (!$user) {
            return back()->withErrors(['code' => 'Necesitas iniciar sesión.']);
        }

        if (!Schema::hasTable('reward_codes')) {
            return back()->withErrors(['code' => 'Aún no hay códigos disponibles.']);
        }

        $codeValue = Str::upper(trim($validated['code']));
        $reward = RewardCode::query()
            ->whereRaw('upper(code) = ?', [$codeValue])
            ->where('is_active', true)
            ->first();

        if (!$reward) {
            return back()->withErrors(['code' => 'Código no válido o caducado.']);
        }

        if ($reward->uses_max !== null && $reward->uses_count >= $reward->uses_max) {
            return back()->withErrors(['code' => 'Este código ya se ha usado.']);
        }

        if (!empty($reward->used_by_user_id)) {
            return back()->withErrors(['code' => 'Este código ya se ha usado.']);
        }

        $character = null;
        if (Schema::hasTable('characters')) {
            $character = Character::where('user_id', $user->id)->first();
        }

        if (!$character) {
            return back()->withErrors(['code' => 'Necesitas un personaje para canjear este código.']);
        }

        $payload = is_array($reward->payload_json ?? null) ? $reward->payload_json : [];
        $type = Str::lower($reward->type);

        $aplicado = false;

        DB::transaction(function () use ($reward, $character, $payload, $type, &$aplicado) {
            if ($type === 'gold') {
                if (!Schema::hasColumn('characters', 'gold')) {
                    return;
                }
                $amount = (int) ($payload['gold'] ?? 0);
                if ($amount <= 0) {
                    return;
                }
                $character->gold = (int) ($character->gold ?? 0) + $amount;
                $character->save();
                $aplicado = true;
            }

            if ($type === 'mount') {
                if (!Schema::hasTable('mounts')) {
                    return;
                }
                $mountId = (int) ($payload['mount_id'] ?? 0);
                if ($mountId <= 0 || !Mount::whereKey($mountId)->exists()) {
                    return;
                }
                $character->mount_id = $mountId;
                if (Schema::hasColumn('characters', 'has_mount')) {
                    $character->has_mount = true;
                }
                $character->save();
                $aplicado = true;
            }

            if ($type === 'item') {
                if (!Schema::hasTable('character_items') || !Schema::hasTable('items')) {
                    return;
                }
                $itemId = (int) ($payload['item_id'] ?? 0);
                $quantity = max(1, (int) ($payload['quantity'] ?? 1));
                if ($itemId <= 0 || !Item::whereKey($itemId)->exists()) {
                    return;
                }

                $row = CharacterItem::firstOrNew([
                    'character_id' => $character->id,
                    'item_id' => $itemId,
                ]);
                $row->quantity = (int) ($row->quantity ?? 0) + $quantity;
                $row->save();
                $aplicado = true;
            }

            if (!$aplicado) {
                return;
            }

            $reward->uses_count = (int) $reward->uses_count + 1;
            $reward->used_by_user_id = $character->user_id;
            $reward->used_at = now();
            if ($reward->uses_max !== null && $reward->uses_count >= $reward->uses_max) {
                $reward->is_active = false;
            }
            $reward->save();
        });

        if (!$aplicado) {
            return back()->withErrors(['code' => 'El código no se pudo aplicar.']);
        }

        return back()->with('code-status', 'Código aplicado correctamente.');
    }
}
