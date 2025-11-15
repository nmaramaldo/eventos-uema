<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventosSeeder extends Seeder
{
    public function run()
    {
        $eventosEspecificos = [
            [
                'nome' => 'Workshop de Laravel Avançado',
                'descricao' => 'Workshop completo sobre recursos avançados do Laravel incluindo APIs, testes e deploy.',
                'tipo_classificacao' => 'Workshop',
                'area_tematica' => 'Tecnologia',
                'data_inicio_evento' => '2024-03-15 09:00:00',
                'data_fim_evento' => '2024-03-15 18:00:00',
                'data_inicio_inscricao' => '2024-02-01 00:00:00',
                'data_fim_inscricao' => '2024-03-14 23:59:00',
                'tipo_evento' => 'presencial',
                'tipo_pagamento' => 'gratis',
                'detalhes_pagamento' => null,
                'status' => 'ativo',
                'vagas' => 40,
                'link_reuniao' => null,
                'link_app' => 'https://meet.google.com/abc-def-ghi',
            ],
            [
                'nome' => 'Conferência Anual de Tecnologia',
                'descricao' => 'A maior conferência de tecnologia da região com palestrantes nacionais e internacionais.',
                'tipo_classificacao' => 'Conferência',
                'area_tematica' => 'Tecnologia',
                'data_inicio_evento' => '2024-04-20 08:00:00',
                'data_fim_evento' => '2024-04-22 17:00:00',
                'data_inicio_inscricao' => '2024-03-01 00:00:00',
                'data_fim_inscricao' => '2024-04-19 23:59:00',
                'tipo_evento' => 'hibrido',
                'tipo_pagamento' => 'outros',
                'detalhes_pagamento' => 'Valor: R$ 250,00 - Inclui coffee break e material - Pagamento via PIX ou cartão',
                'status' => 'publicado',
                'vagas' => 500,
                'link_reuniao' => 'https://zoom.us/j/123456789',
                'link_app' => 'https://eventapp.com/conferencia-tech',
            ],
        ];

        foreach ($eventosEspecificos as $evento) {
            Event::create($evento);
        }

        // Criar eventos aleatórios
        Event::factory()->count(8)->gratuito()->presencial()->publicado()->create();
        Event::factory()->count(8)->comPix()->online()->ativo()->create();
        Event::factory()->count(7)->comPagamentoPersonalizado()->hibrido()->publicado()->create();
        Event::factory()->count(5)->rascunho()->create();

        $this->command->info('30 eventos criados com sucesso!');
    }
}