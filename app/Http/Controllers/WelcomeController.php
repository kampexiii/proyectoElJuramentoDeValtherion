<?php

namespace App\Http\Controllers;

use App\Services\MonthlyChronicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function index(MonthlyChronicleService $svc)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $data = $svc->previousMonth();
        return view('guest.welcome', $data);
    }
}
