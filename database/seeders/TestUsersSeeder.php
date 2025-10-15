<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Roda este seeder apenas em ambiente de desenvolvimento
        if (!app()->environment('local')) {
            return;
        }

        // Usuário Administrador
        User::updateOrCreate(
            ['email' => 'admin@uema.br'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('SenhaForte123!'),
                'tipo_usuario' => UserType::ADMIN,
                'cpf' => '887.225.200-82'
            ]
        );

        // Usuário Comum
        User::updateOrCreate(
            ['email' => 'comum@uema.br'],
            [
                'name' => 'Usuário Comum',
                'password' => Hash::make('SenhaForte123!'),
                'cpf' => '308.913.440-39'
            ]
        );
    }
}