<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }

    // Calcular mes anterior
    $now = now();
    $prev = $now->copy()->subMonth();
    $py = (int) $prev->format('Y');
    $pm = (int) $prev->format('n');

    $season = null;
    if (Schema::hasTable('seasons')) {
        $season = DB::table('seasons')->where('year', $py)->where('month', $pm)->first();
    }

    $rankings = collect();
    $winner = null;

    if ($season) {
        if (Schema::hasTable('season_race_rankings') && Schema::hasTable('races')) {
            $rankings = DB::table('season_race_rankings')
                ->where('season_id', $season->id)
                ->join('races', 'season_race_rankings.race_id', '=', 'races.id')
                ->select('season_race_rankings.race_id', 'season_race_rankings.points', 'races.name as race_name')
                ->orderByDesc('season_race_rankings.points')
                ->limit(10)
                ->get();
        }

        if (Schema::hasTable('season_race_winners')) {
            $winner = DB::table('season_race_winners')->where('season_id', $season->id)->first();
            if ($winner && Schema::hasTable('races')) {
                $winner->race_name = DB::table('races')->where('id', $winner->race_id)->value('name');
            }
        }
    }

    // Fallback estable: si no hay rankings, usar orden por nombre de raza (A). Si no existe 'races', mostrar mensaje (C).
    $fallbackUsed = null;
    $fallbackMessage = null;
    if ($rankings->isEmpty()) {
        if (Schema::hasTable('races')) {
            $races = DB::table('races')->select('id', 'name')->orderBy('name', 'asc')->get();
            $rankings = $races->values()->map(function ($r, $i) {
                return (object) [
                    'race_id' => $r->id,
                    'race_name' => $r->name ?? ('Raza #' . $r->id),
                    'points' => 0,
                    'place' => $i + 1,
                ];
            });
            $fallbackUsed = 'A';
        } else {
            $fallbackMessage = 'Aún no hay clasificación registrada. Falta cargar razas en la base de datos.';
            $fallbackUsed = 'C';
        }
    }

    return view('guest.welcome', [
        'previousSeason' => $season,
        'seasonRankings' => $rankings,
        'seasonWinner' => $winner,
        'fallbackUsed' => $fallbackUsed,
        'fallbackMessage' => $fallbackMessage,
    ]);
});

// Se removió la ruta dedicada /prologo: el prólogo ahora es una sección
// incluida directamente en la landing (`guest.sections.prologo`).

Route::get('/home', function () {
    // Calcular mes anterior
    $now = now();
    $prev = $now->copy()->subMonth();
    $py = (int) $prev->format('Y');
    $pm = (int) $prev->format('n');

    $season = null;
    if (Schema::hasTable('seasons')) {
        $season = DB::table('seasons')->where('year', $py)->where('month', $pm)->first();
    }

    $rankings = collect();
    $winner = null;

    if ($season) {
        if (Schema::hasTable('season_race_rankings') && Schema::hasTable('races')) {
            $rankings = DB::table('season_race_rankings')
                ->where('season_id', $season->id)
                ->join('races', 'season_race_rankings.race_id', '=', 'races.id')
                ->select('season_race_rankings.race_id', 'season_race_rankings.points', 'races.name as race_name')
                ->orderByDesc('season_race_rankings.points')
                ->limit(10)
                ->get();
        }

        if (Schema::hasTable('season_race_winners')) {
            $winner = DB::table('season_race_winners')->where('season_id', $season->id)->first();
            if ($winner && Schema::hasTable('races')) {
                $winner->race_name = DB::table('races')->where('id', $winner->race_id)->value('name');
            }
        }
    }

    // Fallback estable: si no hay rankings, usar orden por nombre de raza (A). Si no existe 'races', mostrar mensaje (C).
    $fallbackUsed = null;
    $fallbackMessage = null;
    if ($rankings->isEmpty()) {
        if (Schema::hasTable('races')) {
            $races = DB::table('races')->select('id', 'name')->orderBy('name', 'asc')->get();
            $rankings = $races->values()->map(function ($r, $i) {
                return (object) [
                    'race_id' => $r->id,
                    'race_name' => $r->name ?? ('Raza #' . $r->id),
                    'points' => 0,
                    'place' => $i + 1,
                ];
            });
            $fallbackUsed = 'A';
        } else {
            $fallbackMessage = 'Aún no hay clasificación registrada. Falta cargar razas en la base de datos.';
            $fallbackUsed = 'C';
        }
    }

    return view('game.home.index', [
        'previousSeason' => $season,
        'seasonRankings' => $rankings,
        'seasonWinner' => $winner,
        'fallbackUsed' => $fallbackUsed,
        'fallbackMessage' => $fallbackMessage,
    ]);
})->middleware(['auth', 'verified'])->name('home');

// Rutas del Juego (Placeholders)
Route::middleware(['auth', 'verified'])->prefix('game')->group(function () {
    Route::get('/tienda', function () {
        return view('game.tienda');
    })->name('game.tienda');
    Route::get('/inventario', function () {
        return view('game.inventario');
    })->name('game.inventario');
    Route::get('/perfil', function () {
        return view('game.perfil');
    })->name('game.perfil');
    Route::get('/ajustes', function () {
        return view('game.ajustes');
    })->name('game.ajustes');
    Route::get('/misiones', function () {
        return view('game.misiones');
    })->name('game.misiones');
    Route::get('/peleas', function () {
        return view('game.peleas');
    })->name('game.peleas');
    Route::get('/chat', function () {
        return view('game.chat');
    })->name('game.chat');
});

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Panel de Administración
Route::get('/admin', function () {
    return view('admin.index');
})->middleware(['auth', 'role:admin'])->name('admin.index');

require __DIR__ . '/auth.php';
