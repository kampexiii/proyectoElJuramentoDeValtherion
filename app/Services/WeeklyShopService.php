<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ShopWeeklyOffer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WeeklyShopService
{
    public function getWeeklyOffers(?Carbon $date = null): array
    {
        $date = $date ? $date->copy() : now();
        $weekYear = (int) $date->isoWeekYear;
        $weekNumber = (int) $date->isoWeek;
        $seed = $weekYear . '-' . $weekNumber;

        $armor = collect();
        $weapon = collect();
        $accessory = collect();
        $mount = null;

        $hasItems = Schema::hasTable('items');
        $hasWeekly = Schema::hasTable('shop_weekly_offers');

        if ($hasWeekly) {
            $offers = ShopWeeklyOffer::query()
                ->with('item')
                ->where('week_year', $weekYear)
                ->where('week_number', $weekNumber)
                ->get();

            if ($hasItems) {
                $offers = $this->ensureWeeklyOffers($weekYear, $weekNumber, $offers);
            }

            $armor = $offers->where('category', 'armor')->sortBy('position')->pluck('item')->filter()->values();
            $weapon = $offers->where('category', 'weapon')->sortBy('position')->pluck('item')->filter()->values();
            $accessory = $offers->where('category', 'accessory')->sortBy('position')->pluck('item')->filter()->values();
            $mount = optional($offers->firstWhere('category', 'mount'))->item;
        } elseif ($hasItems) {
            $armorCandidates = $this->getArmorCandidates();
            $weaponCandidates = $this->getWeaponCandidates();
            $accessoryCandidates = $this->getAccessoryCandidates();
            $mountCandidates = $this->getMountCandidates();

            $armor = $this->deterministicPick($armorCandidates, 3, $seed . '|armor');
            $weapon = $this->deterministicPick($weaponCandidates, 3, $seed . '|weapon');
            $accessory = $this->deterministicPick($accessoryCandidates, 3, $seed . '|accessory');
            $mount = $mountCandidates->first();
        }

        $start = (clone $date)->setISODate($weekYear, $weekNumber)->startOfWeek(Carbon::MONDAY);
        $end = (clone $start)->endOfWeek(Carbon::SUNDAY);

        return [
            'armor' => $armor,
            'weapon' => $weapon,
            'accessory' => $accessory,
            'mount' => $mount,
            'weekLabel' => 'Semana ' . $weekNumber . ', ' . $weekYear,
            'weekRange' => $start->format('d/m') . ' - ' . $end->format('d/m'),
        ];
    }

    private function ensureWeeklyOffers(int $weekYear, int $weekNumber, Collection $existing): Collection
    {
        return DB::transaction(function () use ($weekYear, $weekNumber, $existing) {
            $seed = $weekYear . '-' . $weekNumber;

            $armorCandidates = $this->getArmorCandidates();
            $weaponCandidates = $this->getWeaponCandidates();
            $accessoryCandidates = $this->getAccessoryCandidates();
            $mountCandidates = $this->getMountCandidates();

            $this->ensureCategoryOffers($weekYear, $weekNumber, 'armor', $armorCandidates, 3, $seed . '|armor');
            $this->ensureCategoryOffers($weekYear, $weekNumber, 'weapon', $weaponCandidates, 3, $seed . '|weapon');
            $this->ensureCategoryOffers($weekYear, $weekNumber, 'accessory', $accessoryCandidates, 3, $seed . '|accessory');
            $this->ensureMountOffer($weekYear, $weekNumber, $mountCandidates);

            return ShopWeeklyOffer::query()
                ->with('item')
                ->where('week_year', $weekYear)
                ->where('week_number', $weekNumber)
                ->get();
        });
    }

    private function ensureCategoryOffers(
        int $weekYear,
        int $weekNumber,
        string $category,
        Collection $candidates,
        int $limit,
        string $seed
    ): void {
        if ($candidates->isEmpty()) {
            return;
        }

        $existing = ShopWeeklyOffer::query()
            ->where('week_year', $weekYear)
            ->where('week_number', $weekNumber)
            ->where('category', $category)
            ->orderBy('position')
            ->get();

        $occupiedPositions = $existing->pluck('position')->all();
        $usedItemIds = $existing->pluck('item_id')->filter()->all();

        $neededPositions = array_values(array_diff(range(1, $limit), $occupiedPositions));
        if (empty($neededPositions)) {
            return;
        }

        $available = $candidates->whereNotIn('id', $usedItemIds)->values();
        if ($available->isEmpty()) {
            return;
        }

        $selected = $this->deterministicPick($available, count($neededPositions), $seed);
        foreach ($neededPositions as $index => $position) {
            $item = $selected->get($index);
            if (!$item) {
                continue;
            }

            ShopWeeklyOffer::updateOrCreate(
                [
                    'week_year' => $weekYear,
                    'week_number' => $weekNumber,
                    'category' => $category,
                    'position' => $position,
                ],
                [
                    'item_id' => $item->id,
                ]
            );
        }
    }

    private function ensureMountOffer(int $weekYear, int $weekNumber, Collection $candidates): void
    {
        $existing = ShopWeeklyOffer::query()
            ->where('week_year', $weekYear)
            ->where('week_number', $weekNumber)
            ->where('category', 'mount')
            ->first();

        if ($existing || $candidates->isEmpty()) {
            return;
        }

        $fallback = $candidates->first();
        if (!$fallback) {
            return;
        }

        ShopWeeklyOffer::updateOrCreate(
            [
                'week_year' => $weekYear,
                'week_number' => $weekNumber,
                'category' => 'mount',
                'position' => 1,
            ],
            [
                'item_id' => $fallback->id,
            ]
        );
    }

    private function deterministicPick(Collection $items, int $limit, string $seed): Collection
    {
        if ($items->isEmpty()) {
            return collect();
        }

        return $items
            ->sortBy(function ($item) use ($seed) {
                $key = $item->code ?: (string) $item->id;
                return hash('sha256', $seed . '|' . $key);
            })
            ->values()
            ->take($limit);
    }

    private function getArmorCandidates(): Collection
    {
        return Item::query()
            ->where(function ($query) {
                $query->whereIn('type', ['armor', 'armadura', 'casco'])
                    ->orWhereIn('slot', ['head', 'chest', 'helmet', 'armor', 'casco', 'armadura']);
            })
            ->orderBy('code')
            ->orderBy('id')
            ->get();
    }

    private function getWeaponCandidates(): Collection
    {
        return Item::query()
            ->where(function ($query) {
                $query->whereIn('type', ['weapon', 'arma'])
                    ->orWhereIn('slot', ['weapon', 'arma']);
            })
            ->orderBy('code')
            ->orderBy('id')
            ->get();
    }

    private function getAccessoryCandidates(): Collection
    {
        return Item::query()
            ->where(function ($query) {
                $query->whereIn('type', ['accessory', 'accesorio'])
                    ->orWhereIn('slot', ['ring', 'amulet', 'anillo', 'colgante', 'talisman']);
            })
            ->orderBy('code')
            ->orderBy('id')
            ->get();
    }

    private function getMountCandidates(): Collection
    {
        return Item::query()
            ->where(function ($query) {
                $query->whereIn('type', ['mount', 'montura'])
                    ->orWhereIn('slot', ['mount', 'montura']);
            })
            ->orderBy('code')
            ->orderBy('id')
            ->get();
    }
}
