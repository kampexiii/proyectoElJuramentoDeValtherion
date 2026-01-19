<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@valtherion.local'],
            [
                'name'     => 'Admin Valtherion Leal',
                'password' => Hash::make('12345678'),
                'plan'     => 'admin', // Usamos el campo 'plan' como rol
            ]
        );

        // Aseguramos que sea admin si ya existía pero tenía otro rol
        if ($admin->plan !== 'admin') {
            $admin->update(['plan' => 'admin']);
        }
    }
}
