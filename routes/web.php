<?php

use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index']);

// Se removió la ruta dedicada /prologo: el prólogo ahora es una sección
// incluida directamente en la landing (`guest.sections.prologo`).

Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

// Rutas del Juego (Placeholders)
Route::middleware(['auth', 'verified'])->prefix('game')->group(function () {
    Route::get('/personaje/crear', [CharacterController::class, 'create'])->name('game.personaje.create');
    Route::post('/personaje', [CharacterController::class, 'store'])->name('game.personaje.store');
    Route::get('/personaje/editar', [CharacterController::class, 'edit'])->name('game.personaje.edit');
    Route::put('/personaje', [CharacterController::class, 'update'])->name('game.personaje.update');
    Route::delete('/personaje', [CharacterController::class, 'destroy'])->name('game.personaje.destroy');

    Route::get('/perfil', function () {
        return view('game.perfil');
    })->name('game.perfil');
    Route::get('/ajustes', function () {
        return view('game.ajustes');
    })->name('game.ajustes');

    Route::middleware('has.character')->group(function () {
        Route::get('/tienda', function () {
            return view('game.tienda');
        })->name('game.tienda');
        Route::get('/inventario', function () {
            return view('game.inventario');
        })->name('game.inventario');
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
Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store'])->name('admin.login.store');

Route::get('/admin', function () {
    return view('admin.index');
})->middleware(['auth', 'role:admin'])->name('admin.index');

require __DIR__ . '/auth.php';
