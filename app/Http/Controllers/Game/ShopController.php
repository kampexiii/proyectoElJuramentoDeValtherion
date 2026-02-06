<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\CharacterItem;
use App\Models\Item;
use App\Services\WeeklyShopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShopController extends Controller
{
    public function index(Request $request, WeeklyShopService $service)
    {
        $data = $service->getWeeklyOffers();

        return view('game.tienda', [
            'offersArmor' => $data['armor'] ?? collect(),
            'offersWeapon' => $data['weapon'] ?? collect(),
            'offersAccessory' => $data['accessory'] ?? collect(),
            'offerMount' => $data['mount'] ?? null,
            'weekLabel' => $data['weekLabel'] ?? null,
            'weekRange' => $data['weekRange'] ?? null,
        ]);
    }

    public function buy(Request $request, WeeklyShopService $service)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'integer'],
        ]);

        $user = $request->user();
        if (!$user) {
            return back()->withErrors(['shop' => 'Necesitas iniciar sesión para comprar.']);
        }

        if (!Schema::hasTable('items') || !Schema::hasTable('character_items')) {
            return back()->withErrors(['shop' => 'Aún no hay inventario activo.']);
        }

        if (!Schema::hasTable('characters')) {
            return back()->withErrors(['shop' => 'Necesitas un personaje para comprar.']);
        }

        $character = $user->character;
        if (!$character) {
            return back()->withErrors(['shop' => 'Necesitas un personaje para comprar.']);
        }

        if (!Schema::hasColumn('characters', 'gold')) {
            return back()->withErrors(['shop' => 'Aún no hay sistema de oro activo.']);
        }

        $item = Item::find($validated['item_id']);
        if (!$item) {
            return back()->withErrors(['shop' => 'El objeto no existe.']);
        }

        $offers = $service->getWeeklyOffers();
        $availableIds = collect()
            ->merge($offers['armor'] ?? collect())
            ->merge($offers['weapon'] ?? collect())
            ->merge($offers['accessory'] ?? collect())
            ->merge(collect([$offers['mount'] ?? null]))
            ->filter()
            ->pluck('id')
            ->all();

        if (!in_array($item->id, $availableIds, true)) {
            return back()->withErrors(['shop' => 'Ese objeto no está disponible esta semana.']);
        }

        $price = (int) ($item->value_gold ?? $item->sell_price ?? 0);
        if ($price <= 0) {
            return back()->withErrors(['shop' => 'Ese objeto no tiene precio válido.']);
        }

        $gold = (int) ($character->gold ?? 0);
        if ($gold < $price) {
            return back()->withErrors(['shop' => 'No tienes suficiente oro.']);
        }

        DB::transaction(function () use ($character, $item, $price) {
            $character->gold = max(0, (int) $character->gold - $price);
            $character->save();

            $row = CharacterItem::firstOrNew([
                'character_id' => $character->id,
                'item_id' => $item->id,
            ]);
            $row->quantity = (int) ($row->quantity ?? 0) + 1;
            $row->save();
        });

        return back()->with('shop-status', 'Compra realizada. El objeto fue agregado a tu inventario.');
    }
}
