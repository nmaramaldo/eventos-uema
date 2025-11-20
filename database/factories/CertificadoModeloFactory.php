<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

use function Symfony\Component\Clock\now;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CertificadoModelo>
 */
class CertificadoModeloFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evento_id' => Event::factory(),
            'titulo' => $this->faker->sentence(3),
            'slug_tipo' => $this->faker->slug(),
            'atribuicao' => $this->faker->name(),
            'publicado' => true,
            'corpo_html' => '<div>Certificado de Participação</div>',
            'background_path' => $this->faker->imageUrl(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
