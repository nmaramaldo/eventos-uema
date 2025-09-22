<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Usuário MASTER (sempre)
        $this->call([
            MasterUserSeeder::class,
        ]);

        // 2) Usuário de teste (apenas em ambiente local)
        if (app()->environment('local')) {
            User::factory()->create([
                'name'         => 'Test User',
                'email'        => 'test@example.com',
                // senha padrão da factory geralmente é 'password'
                'tipo_usuario' => 'comum',
                'ativo'        => true,
            ]);
        }
    }
}
