<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ShopWeeklyOffer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ShopController extends Controller
{
    public function index()
    {
        $date = now();
        $weekYear = (int) $date->isoWeekYear;
        $weekNumber = (int) $date->isoWeek;
        $weekLabel = 'Semana ' . $weekNumber . ', ' . $weekYear;

        $mounts = collect();
        if (Schema::hasTable('items')) {
            $mounts = Item::query()
                ->where(function ($query) {
                    $query->where('type', 'mount')->orWhere('slot', 'mount');
                })
                ->orderBy('name')
                ->get();
        }

        $currentMount = null;
        if (Schema::hasTable('shop_weekly_offers')) {
            $currentMount = ShopWeeklyOffer::query()
                ->with('item')
                ->where('week_year', $weekYear)
                ->where('week_number', $weekNumber)
                ->where('category', 'mount')
                ->first();
        }

        return view('admin.shop', [
            'weekLabel' => $weekLabel,
            'mounts' => $mounts,
            'currentMount' => $currentMount?->item,
            'hasShopTable' => Schema::hasTable('shop_weekly_offers'),
        ]);
    }

    public function storeMount(Request $request)
    {
        if (!Schema::hasTable('shop_weekly_offers')) {
            return back()->withErrors(['item_id' => 'La tabla de tienda semanal no existe aún.']);
        }

        $validated = $request->validate([
            'item_id' => ['required', 'integer'],
        ]);

        if (!Schema::hasTable('items')) {
            return back()->withErrors(['item_id' => 'No hay objetos cargados en la tienda.']);
        }

        $item = Item::query()->whereKey($validated['item_id'])->first();
        $isMount = $item && (in_array($item->type, ['mount'], true) || $item->slot === 'mount');
        if (!$item || !$isMount) {
            return back()->withErrors(['item_id' => 'Selecciona una montura válida.']);
        }

        $date = Carbon::now();
        $weekYear = (int) $date->isoWeekYear;
        $weekNumber = (int) $date->isoWeek;

        ShopWeeklyOffer::updateOrCreate(
            [
                'week_year' => $weekYear,
                'week_number' => $weekNumber,
                'category' => 'mount',
                'position' => 1,
            ],
            [
                'item_id' => $item->id,
            ]
        );

        return back()->with('mount-status', 'Montura semanal actualizada.');
    }
}
