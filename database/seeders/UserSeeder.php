<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@hospital.gob.ve',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->assignRole('administrador');

        // Algunos usuarios adicionales
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Empleado $i",
                'email' => "empleado$i@hospital.gob.ve",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user->assignRole('usuario');
        }
    }
}
