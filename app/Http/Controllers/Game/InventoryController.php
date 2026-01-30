<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventory = collect();
        $character = $request->user()?->character;

        if ($character && Schema::hasTable('character_items')) {
            $inventory = $character->inventory()->with('item')->get();
        }

        return view('game.inventario', [
            'inventory' => $inventory,
        ]);
    }
}
