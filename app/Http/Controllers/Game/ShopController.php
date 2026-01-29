<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Services\WeeklyShopService;
use Illuminate\Http\Request;

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
}
