<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EventFactory extends Factory
{

    protected $model = Event::class;

    public function definition(): array
    {
        $dataInicioEvento = $this->faker->dateTimeBetween('2025', '+6 months');
        $dataFimEvento = Carbon::parse($dataInicioEvento)->addHours($this->faker->numberBetween(1, 5));

        $dataInicioInscricao = Carbon::parse($dataInicioEvento)->subDays($this->faker->numberBetween(7, 30));
        $dataFimInscricao = Carbon::parse($dataInicioEvento)->subDays(1);

        return [
            'nome' => $this->faker->randomElement([
                'Workshop de ',
                'Palestra Sobre',
                'Curso de ',
                'Encontro de ',
                'Conferência de ',
                'Seminário de ',
                'Treinamento de ',
                'Meetup de '
            ]) . $this->faker->words(2, true),
            'descricao' => $this->faker->paragraph(3),
            'tipo_classificacao' => $this->faker->randomElement([
                'Workshop',
                'Palestra',
                'Curso',
                'Conferência',
                'Seminário',
                'Treinamento',
                'Meetup',
                'Simpósio'
            ]),

            'area_tematica' => $this->faker->randomElement([
                'Tecnologia',
                'Educação',
                'Saúde',
                'Negócios',
                'Artes',
                'Ciências',
                'Engenharia',
                'Humanidades'
            ]),

            'data_inicio_evento' => $dataInicioEvento,
            'data_fim_evento' => $dataFimEvento,
            'data_inicio_inscricao' => $dataInicioInscricao,
            'data_fim_inscricao' => $dataFimInscricao,

            'tipo_evento' => $this->faker->randomElement([
                'presencial',
                'online',
                'hibrido'
            ]),

            
            'tipo_pagamento' => $this->faker->randomElement([
                'gratis',
                'pix',
                'outros'
            ]),

            'detalhes_pagamento' => function (array $attributes) {
                return $attributes['tipo_pagamento'] === 'outros'
                    ? 'Valor: R$ ' . $this->faker->randomElement([50, 100, 150, 200, 300]) . ',00 - Pagamento via transferência bancária'
                    : null;
            },

            // Campos NULLABLE  

            'logomarca_path' => null,
            'status' => $this->faker->randomElement([
                'rascunho',
                'publicado',
                'ativo'
            ]),

            'vagas' => $this->faker->numberBetween(10, 300),

            'link_reuniao' => function (array $attributes) {
                return in_array($attributes['tipo_evento'], ['online', 'hibrido'])
                    ? $this->faker->url()
                    : null;
            },

            'link_app' => $this->faker->optional(0.3)->url(), // 30% de chance de ter link

            'created_at' => now(),
            'updated_at' => now(),

        ];
    }

    // Estados customizados para facilitar 
    public function gratuito()
    {
        return $this->state([
            'tipo_pagamento' => 'gratis',
            'detalhes_pagamento' => null,
        ]);
    }

    public function comPix()
    {
        return $this->state([
            'tipo_pagamento' => 'pix',
            'detalhes_pagamento' => null,
        ]);
    }

    public function comPagamentoPersonalizado()
    {
        return $this->state([
            'tipo_pagamento' => 'outros',
            'detalhes_pagamento' => 'Valor: R$ ' . $this->faker->randomElement([50, 100, 150, 200, 300]) . ',00 - Pagamento via transferência bancária',
        ]);
    }

    public function online()
    {
        return $this->state([
            'tipo_evento' => 'online',
            'link_reuniao' => $this->faker->url(),
        ]);
    }

    public function presencial()
    {
        return $this->state([
            'tipo_evento' => 'presencial',
            'link_reuniao' => null,
        ]);
    }

    public function hibrido()
    {
        return $this->state([
            'tipo_evento' => 'hibrido',
            'link_reuniao' => $this->faker->url(),
        ]);
    }

    public function publicado()
    {
        return $this->state([
            'status' => 'publicado'
        ]);
    }

    public function ativo()
    {
        return $this->state([
            'status' => 'ativo',
        ]);
    }

    public function rascunho()
    {
        return $this->state([
            'status' => 'rascunho',
        ]);
    }
}
