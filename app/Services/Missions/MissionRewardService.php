<?php

declare(strict_types=1);

namespace App\Services\Missions;

use App\Models\CharacterItem;
use App\Models\MissionReward;
use Illuminate\Support\Facades\Schema;

class MissionRewardService
{
    /**
     * @return array{applied: bool, messages: string[]}
     */
    public function applyRewards(MissionReward $reward, int $characterId, ?array $itemsJson, bool $alreadyApplied): array
    {
        if ($alreadyApplied) {
            return ['applied' => false, 'messages' => ['Las recompensas ya fueron aplicadas.']];
        }

        $messages = [];

        if ($reward->gold > 0) {
            \App\Models\Character::query()
                ->whereKey($characterId)
                ->increment('gold', (int) $reward->gold);
        }

        if ($reward->xp > 0) {
            \App\Models\Character::query()
                ->whereKey($characterId)
                ->increment('xp', (int) $reward->xp);
        }

        if (!Schema::hasTable('items')) {
            if (!empty($itemsJson)) {
                $messages[] = 'Items no disponibles aun.';
            }
            return ['applied' => true, 'messages' => $messages];
        }

        if (!empty($itemsJson)) {
            foreach ($itemsJson as $row) {
                $itemId = (int) ($row['item_id'] ?? 0);
                $qty = (int) ($row['qty'] ?? 0);
                if ($itemId <= 0 || $qty <= 0) {
                    continue;
                }

                $existing = CharacterItem::query()
                    ->where('character_id', $characterId)
                    ->where('item_id', $itemId)
                    ->first();

                if ($existing) {
                    $existing->increment('quantity', $qty);
                } else {
                    CharacterItem::create([
                        'character_id' => $characterId,
                        'item_id' => $itemId,
                        'quantity' => $qty,
                    ]);
                }
            }
        }

        return ['applied' => true, 'messages' => $messages];
    }
}
