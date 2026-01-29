<?php

use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\RewardCodeController as AdminRewardCodeController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterEquipmentController;
use App\Http\Controllers\Game\EquipamientoController;
use App\Http\Controllers\Game\ShopController as GameShopController;
use App\Http\Controllers\GameProfileController;
use App\Http\Controllers\GameRewardCodeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PotionController;
use App\Http\Controllers\WelcomeController;

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

    Route::get('/perfil', [GameProfileController::class, 'show'])->name('game.perfil');
    Route::get('/perfil/disponibilidad', [GameProfileController::class, 'availability'])->name('game.perfil.disponibilidad');
    Route::get('/ajustes', function () {
        return view('game.ajustes');
    })->name('game.ajustes');
    Route::post('/ajustes/codigo', [GameRewardCodeController::class, 'redeem'])->name('game.ajustes.codigo');
    Route::get('/equipamiento', [EquipamientoController::class, 'edit'])->name('game.equipamiento.edit');
    Route::post('/equipamiento', [EquipamientoController::class, 'update'])->name('game.equipamiento.update');

    Route::middleware('has.character')->group(function () {
        Route::post('/personaje/equipar', [CharacterEquipmentController::class, 'equip'])->name('game.personaje.equipar');
        Route::post('/personaje/desequipar', [CharacterEquipmentController::class, 'unequip'])->name('game.personaje.desequipar');

        Route::get('/tienda', [GameShopController::class, 'index'])->name('game.tienda');
        Route::get('/inventario', function () {
            $inventory = collect();
            $character = request()->user()?->character;

            if ($character && \Illuminate\Support\Facades\Schema::hasTable('character_items')) {
                $inventory = $character->inventory()->with('item')->get();
            }

            return view('game.inventario', [
                'inventory' => $inventory,
            ]);
        })->name('game.inventario');
        Route::post('/inventario/pociones/usar/{item}', [PotionController::class, 'usePotion'])->name('game.inventario.pociones.usar');
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

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminRewardCodeController::class, 'index'])->name('admin.index');
    Route::post('/codigos', [AdminRewardCodeController::class, 'store'])->name('admin.codigos.store');
    Route::get('/tienda', [AdminShopController::class, 'index'])->name('admin.tienda');
    Route::post('/tienda/montura', [AdminShopController::class, 'storeMount'])->name('admin.tienda.montura');
});

require __DIR__ . '/auth.php';
