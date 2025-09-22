<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        // Pegue do .env se existir; senão usa os defaults
        $email    = env('MASTER_EMAIL', 'master@uema.br');
        $name     = env('MASTER_NAME', 'Usuário Master');
        $password = env('MASTER_PASSWORD', 'SenhaForte123!');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => Hash::make($password),
                'tipo_usuario'      => 'master',   // 'comum' | 'admin' | 'master'
                'ativo'             => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
