<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        // Lê do .env; define defaults se não existir
        $email    = env('MASTER_EMAIL', 'master@uema.br');
        $name     = env('MASTER_NAME', 'Usuário Master');
        $password = env('MASTER_PASSWORD', 'SenhaForte123!');

        // Se quiser evitar resetar a senha todo seed, use essa flag
        $reset = filter_var(env('MASTER_RESET_ON_DEPLOY', false), FILTER_VALIDATE_BOOL);

        $user = User::where('email', $email)->first();

        if (!$user) {
            // cria do zero
            User::create([
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make($password),
                'tipo_usuario'      => 'master',   // mantém string para compatibilidade
                'ativo'             => true,
                'cpf'                 => '202.648.301-06',
                'email_verified_at' => now(),
            ]);
        } else {
            // garante master + ativo
            $user->forceFill([
                'name'         => $name,
                'tipo_usuario' => 'master',
                'ativo'        => true,
            ]);

            if ($reset) {
                $user->password = Hash::make($password);
            }

            $user->save();
        }
    }
}
