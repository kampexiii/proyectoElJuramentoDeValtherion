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
        $targetEmail = 'pablosevillanoa90@gmail.com';
        $oldEmail = 'admin@valtherion.local';

        // 1. Intentar encontrar al admin antiguo para migrarlo
        $admin = User::where('email', $oldEmail)->first();

        if ($admin) {
            // Migrar el usuario antiguo al nuevo email
            $admin->email = $targetEmail;
        } else {
            // 2. Si no existía el antiguo, buscar/crear el nuevo
            $admin = User::firstOrNew(['email' => $targetEmail]);
        }

        // 3. Password solo si el usuario no tiene ID aún (es nuevo de verdad)
        if (!$admin->exists) {
            $admin->password = Hash::make('12345678');
        }

        // 4. Campos fijos e idempotentes
        $admin->name = 'Pablo Sevillano';
        $admin->plan = 'premium';
        $admin->role = 'admin';
        
        $admin->save();
    }
}
