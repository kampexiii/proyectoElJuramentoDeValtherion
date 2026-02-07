<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\RewardCodeController as AdminRewardCodeController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MissionPublishController;
use App\Http\Controllers\Missions\BossBattleController;
use App\Http\Controllers\Missions\MissionController as PlayerMissionController;
use App\Http\Controllers\Missions\MissionRunController;
use App\Http\Controllers\Character\CharacterSheetController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterEquipmentController;
use App\Http\Controllers\Game\EquipamientoController;
use App\Http\Controllers\Game\ShopController as GameShopController;
use App\Http\Controllers\GameProfileController;
use App\Http\Controllers\GameRewardCodeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PotionController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Pvp\BattleRoomController;
use App\Http\Controllers\Pvp\BattleController;

use App\Http\Controllers\ProfileController;

Route::get('/', [WelcomeController::class, 'index']);

Route::view('/legales', 'guest.legal.index')->name('legal.index');
Route::view('/cookies', 'guest.legal.cookies')->name('legal.cookies');
Route::view('/terminos', 'guest.legal.terms')->name('legal.terms');
Route::view('/privacidad', 'guest.legal.privacy')->name('legal.privacy');
Route::view('/lore', 'guest.lore')->name('legal.lore');

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
    Route::post('/equipamiento/stats-preview', [EquipamientoController::class, 'statsPreview'])->middleware('auth');

    Route::middleware('has.character')->group(function () {
        Route::get('/personaje', [CharacterSheetController::class, 'show'])->name('game.personaje.sheet');
        Route::post('/personaje/equipar', [CharacterEquipmentController::class, 'equip'])->name('game.personaje.equipar');
        Route::post('/personaje/desequipar', [CharacterEquipmentController::class, 'unequip'])->name('game.personaje.desequipar');

        Route::get('/tienda', [GameShopController::class, 'index'])->name('game.tienda');
        Route::post('/tienda/comprar', [GameShopController::class, 'buy'])->name('game.tienda.comprar');
        // Inventario ahora redirige a la armería.
        Route::get('/inventario', function () {
            return redirect()->route('game.equipamiento.edit');
        })->name('game.inventario');
        // Uso de pociones desde equipamiento.
        Route::post('/pociones/usar', [PotionController::class, 'usePotionFromSelection'])->name('game.pociones.usar');
        Route::post('/inventario/pociones/usar/{item}', [PotionController::class, 'usePotion'])->name('game.inventario.pociones.usar');
        Route::get('/misiones', [PlayerMissionController::class, 'index'])->name('game.missions.index');
        Route::get('/misiones/{mission}', [PlayerMissionController::class, 'show'])->name('game.missions.show');
        Route::post('/misiones/{mission}/start', [MissionRunController::class, 'start'])->name('game.missions.start');
        Route::get('/misiones/runs/{run}', [MissionRunController::class, 'show'])->name('game.missions.run');
        Route::post('/misiones/runs/{run}/choose', [MissionRunController::class, 'choose'])->name('game.missions.choose');
        Route::post('/misiones/runs/{run}/abandon', [MissionRunController::class, 'abandon'])->name('game.missions.abandon');
        Route::get('/misiones/run/{run}/boss', [BossBattleController::class, 'show'])->name('game.missions.boss.show');
        Route::post('/misiones/run/{run}/boss/action', [BossBattleController::class, 'action'])->name('game.missions.boss.action');
        Route::post('/misiones/runs/{run}/boss', [BossBattleController::class, 'action'])->name('game.missions.boss.fight');
        Route::get('/peleas', function () {
            return redirect()->route('pvp.lobby');
        })->name('game.peleas');
        Route::get('/chat', [ChatController::class, 'index'])->name('game.chat');
        Route::post('/chat/{room}/mensajes', [ChatController::class, 'store'])->name('game.chat.store');
    });
});

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::prefix('pvp')->name('pvp.')->group(function () {
        Route::get('/', [BattleRoomController::class, 'index'])->name('lobby');
        Route::post('/rooms', [BattleRoomController::class, 'store'])->name('rooms.store');
        Route::get('/rooms/{room}', [BattleRoomController::class, 'show'])->name('rooms.show');
        Route::post('/rooms/{room}/join', [BattleRoomController::class, 'join'])->name('rooms.join');
        Route::get('/rooms/{room}/battle', [BattleController::class, 'show'])->name('rooms.battle');
        Route::post('/rooms/{room}/battle', [BattleController::class, 'submit'])->name('rooms.battle.submit');
        Route::post('/rooms/{room}/close', [BattleRoomController::class, 'close'])->name('rooms.close');
    });

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
    Route::get('/usuarios', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/usuarios', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::patch('/usuarios/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/usuarios/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    Route::resource('final-bosses', 'App\\Http\\Controllers\\Admin\\FinalBossController');
    Route::resource('missions', 'App\\Http\\Controllers\\Admin\\MissionController');
    Route::resource('missions.nodes', 'App\\Http\\Controllers\\Admin\\MissionNodeController');
    Route::resource('missions.nodes.choices', 'App\\Http\\Controllers\\Admin\\MissionChoiceController');
    Route::post('missions/{mission}/publish', [MissionPublishController::class, 'store'])->name('missions.publish');
});

require __DIR__ . '/auth.php';
