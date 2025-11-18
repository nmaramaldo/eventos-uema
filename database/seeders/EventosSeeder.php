<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Inscricao;

class EventosSeeder extends Seeder
{
    public function run()
    {
        // -------------------------------------------------
        // 1) Criar usuários que serão inscritos nos eventos
        // -------------------------------------------------
        $usuarios = User::factory(40)->create();

        // -------------------------------------------------
        // 2) Eventos específicos
        // -------------------------------------------------
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

        // Insere eventos específicos
        foreach ($eventosEspecificos as $evento) {
            Event::create($evento);
        }

        // -------------------------------------------------
        // 3) Criar eventos gerados pelo factory
        // -------------------------------------------------
        $eventos = collect();

        $eventos = $eventos->merge(
            Event::factory()->count(8)->gratuito()->presencial()->publicado()->create()
        );
        $eventos = $eventos->merge(
            Event::factory()->count(8)->comPix()->online()->ativo()->create()
        );
        $eventos = $eventos->merge(
            Event::factory()->count(7)->comPagamentoPersonalizado()->hibrido()->publicado()->create()
        );
        $eventos = $eventos->merge(
            Event::factory()->count(5)->rascunho()->create()
        );

       
        // Pega também os eventos específicos que já têm imagem
        $todosEventos = Event::all();

        // -------------------------------------------------
        // 5) Criar inscrições para todos os eventos
        // -------------------------------------------------
       foreach ($todosEventos as $evento) {

    // garante pelo menos 30 inscrições por evento
    $inscritos = $usuarios->shuffle()->take(30);

        foreach ($inscritos as $user) {
            Inscricao::create([
                'user_id'  => $user->id,
                'evento_id' => $evento->id,
                'status'   => 'confirmada',
            ]);
        }
    }

    $this->command->info('Eventos e inscrições criados com sucesso (mínimo 30 por evento)!');

    }
    
}
