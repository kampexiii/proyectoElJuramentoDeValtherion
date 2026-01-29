<?php

namespace App\Http\Controllers;

use App\Services\MonthlyChronicleService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(MonthlyChronicleService $svc, Request $request)
    {
        $data = $svc->previousMonth();
        $data['character'] = $request->user()?->character;

        $spriteUrl = null;
        if ($data['character']) {
            $candidate = public_path("assets/characters/{$data['character']->id}.png");
            if (file_exists($candidate)) {
                $spriteUrl = asset("assets/characters/{$data['character']->id}.png");
            }
        }
        $data['spriteUrl'] = $spriteUrl;

        return view('game.home.index', $data);
    }
}
