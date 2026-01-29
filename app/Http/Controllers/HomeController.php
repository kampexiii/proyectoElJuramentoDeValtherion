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
        return view('game.home.index', $data);
    }
}
