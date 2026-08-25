<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuário Funcionário (Atendimento / Cozinha)
        User::updateOrCreate(
            ['email' => 'funcionario@gmail.com'],
            [
                'name' => 'Funcionário',
                'password' => Hash::make('123'),
            ]
        );

        // Usuário Chefe / Gerente
        User::updateOrCreate(
            ['email' => 'chefe@gmail.com'],
            [
                'name' => 'Chefe',
                'password' => Hash::make('123'),
            ]
        );
    }
}
