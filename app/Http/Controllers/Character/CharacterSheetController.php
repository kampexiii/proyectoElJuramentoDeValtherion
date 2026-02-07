<?php

namespace App\Http\Controllers\Character;

use App\Http\Controllers\Controller;
use App\Services\Stats\CharacterStatsCalculator;
use Illuminate\Http\Request;

class CharacterSheetController extends Controller
{
    public function show(Request $request, CharacterStatsCalculator $calculator)
    {
        $character = $request->user()?->character;
        if (!$character) {
            return redirect()->route('game.personaje.create');
        }

        $breakdown = $calculator->getBreakdown($character);

        return view('character.sheet', [
            'character' => $character,
            'breakdown' => $breakdown,
        ]);
    }
}
