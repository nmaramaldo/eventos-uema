<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Inscricao;
use App\Models\User;
use App\Models\Event;

class InscricaoFactory extends Factory
{
    protected $model = Inscricao::class;

    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'event_id' => Event::factory(),
            'status'   => $this->faker->randomElement(['pendente', 'confirmada', 'cancelada']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
