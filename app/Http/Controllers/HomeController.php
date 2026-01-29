<?php

namespace App\Http\Controllers;

use App\Services\MonthlyChronicleService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(MonthlyChronicleService $svc)
    {
        $data = $svc->previousMonth();
        return view('game.home.index', $data);
    }
}
