<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Robusto: Primero buscamos o instanciamos por email
        $admin = User::firstOrNew(['email' => 'admin@valtherion.local']);

        // Si es nuevo, asignamos password (si ya existe, NO se toca)
        if (!$admin->exists) {
            $admin->password = Hash::make('12345678');
        }

        // Asignamos el resto de campos (idempotente: sobrescribe si hace falta)
        $admin->name = 'Admin Valtherion Leal';
        $admin->plan = 'premium'; // Acceso al contenido
        $admin->role = 'admin';   // Permisos de administrador
        
        $admin->save();
    }
}
