<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('guest.welcome');
});

Route::get('/home', function () {
    return view('game.home.index');
})->middleware(['auth', 'verified'])->name('home');

// Rutas del Juego (Placeholders)
Route::middleware(['auth', 'verified'])->prefix('game')->group(function () {
    Route::get('/tienda', function () { return view('game.tienda'); })->name('game.tienda');
    Route::get('/inventario', function () { return view('game.inventario'); })->name('game.inventario');
    Route::get('/perfil', function () { return view('game.perfil'); })->name('game.perfil');
    Route::get('/ajustes', function () { return view('game.ajustes'); })->name('game.ajustes');
    Route::get('/misiones', function () { return view('game.misiones'); })->name('game.misiones');
    Route::get('/peleas', function () { return view('game.peleas'); })->name('game.peleas');
    Route::get('/chat', function () { return view('game.chat'); })->name('game.chat');
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

require __DIR__.'/auth.php';
