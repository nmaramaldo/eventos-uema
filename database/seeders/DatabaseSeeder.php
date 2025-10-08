<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterUserSeeder::class,    // Sempre cria o usuário Master
            TestUsersSeeder::class,     // Cria Admin e Comum (apenas em 'local')
            LocaisUemaSeeder::class,    // Popula a tabela de locais
            // Outros seeders que você venha a criar...
        ]);
    }
}