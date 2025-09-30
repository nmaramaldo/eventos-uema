<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocaisUemaSeeder extends Seeder
{
    public function run(): void
    {
        $locais = [
            ['nome' => 'Campus Paulo VI', 'tipo' => 'campus', 'campus' => 'Paulo VI'],
            ['nome' => 'Centro de Ciências Agrárias (CCA)', 'tipo' => 'predio', 'campus' => 'Paulo VI', 'predio' => 'CCA'],
            ['nome' => 'Centro de Ciências Tecnológicas (CCT)', 'tipo' => 'predio', 'campus' => 'Paulo VI', 'predio' => 'CCT'],
            ['nome' => 'Centro de Educação, Ciências Exatas e Naturais (CECEN)', 'tipo' => 'predio', 'campus' => 'Paulo VI', 'predio' => 'CECEN'],
            ['nome' => 'Centro de Ciências da Saúde (CCS)', 'tipo' => 'predio', 'campus' => 'Paulo VI', 'predio' => 'CCS'],
            // Exemplos de espaços internos
            ['nome' => 'Auditório CCT', 'tipo' => 'auditorio', 'campus' => 'Paulo VI', 'predio' => 'CCT', 'capacidade' => 300],
            ['nome' => 'Sala 101 - CCT', 'tipo' => 'sala', 'campus' => 'Paulo VI', 'predio' => 'CCT', 'sala' => '101', 'capacidade' => 60],
        ];

        foreach ($locais as $l) {
            DB::table('locais')->insert(array_merge([
                'id' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ], $l));
        }
    }
}
