<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserType; // <-- 1. Importe o Enum
use Illuminate\Support\Facades\Hash;

class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('MASTER_EMAIL', 'master@uema.br');
        $name     = env('MASTER_NAME', 'Usuário Master');
        $password = env('MASTER_PASSWORD', 'SenhaForte123!');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'                => $name,
                'password'            => Hash::make($password),
                'tipo_usuario'        => UserType::MASTER, // <-- 2. Enum
                'email_verified_at'   => now(),
            ]
        );
    }
}