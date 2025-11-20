<?php

namespace Database\Factories;

use App\Models\Certificado;
use App\Models\User;
use App\Models\CertificadoModelo;
use App\Models\Inscricao;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificadoFactory extends Factory
{
    protected $model = Certificado::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'modelo_id' => CertificadoModelo::factory(),
            'inscricao_id' => Inscricao::factory(),
            'url_certificado' => $this->faker->url,
            'data_emissao' => now(),
            'tipo' => $this->faker->randomElement(['participante', 'palestrante', 'organizador']),
            'hash_verificacao' => $this->faker->unique()->sha256,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}