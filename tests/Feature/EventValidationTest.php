<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function nome_is_required()
    {
        $user = User::factory()->create(['tipo_usuario' => 'admin']);

        $response = $this->actingAs($user)->post('app/eventos', [
            'nome' => '', // Campo vazio para forçar erro
            'descricao' => 'Descrição válida',
            'tipo_classificacao' => 'Workshop',
            'area_tematica' => 'Tecnologia',
            'data_inicio_evento' => '2024-03-01 10:00:00',
            'data_fim_evento' => '2024-03-01 12:00:00',
            'data_inicio_inscricao' => '2024-02-01 00:00:00',
            'data_fim_inscricao' => '2024-02-28 23:59:00',
            'tipo_evento' => 'presencial',
            'tipo_pagamento' => 'gratis',
            'vagas' => 50,
        ]);

        // Debug: veja o que está acontecendo
        // dd($response->getContent(), session()->all());
        
        $response->assertSessionHasErrors(['nome']);
    }

    /** @test */
    public function data_fim_evento_must_be_after_data_inicio_evento()
    {
        $user = User::factory()->create(['tipo_usuario' => 'admin']);

        $response = $this->actingAs($user)->post('app/eventos', [
            'nome' => 'Evento Teste',
            'descricao' => 'Descrição válida',
            'tipo_classificacao' => 'Workshop',
            'area_tematica' => 'Tecnologia',
            'data_inicio_evento' => '2024-03-02 10:00:00', // Início
            'data_fim_evento' => '2024-03-01 12:00:00',   // Fim ANTES do início - DEVE DAR ERRO
            'data_inicio_inscricao' => '2024-02-01 00:00:00',
            'data_fim_inscricao' => '2024-02-28 23:59:00',
            'tipo_evento' => 'presencial',
            'tipo_pagamento' => 'gratis',
            'vagas' => 50,
        ]);

        $response->assertSessionHasErrors(['data_fim_evento']);
    }

    /** @test */
    public function data_fim_inscricao_must_be_after_data_inicio_inscricao()
    {
        $user = User::factory()->create(['tipo_usuario' => 'admin']);

        $response = $this->actingAs($user)->post('app/eventos', [
            'nome' => 'Evento Teste',
            'descricao' => 'Descrição válida',
            'tipo_classificacao' => 'Workshop',
            'area_tematica' => 'Tecnologia',
            'data_inicio_evento' => '2024-03-01 10:00:00',
            'data_fim_evento' => '2024-03-01 12:00:00',
            'data_inicio_inscricao' => '2024-02-02 00:00:00', // Início inscrição
            'data_fim_inscricao' => '2024-02-01 23:59:00',    // Fim ANTES do início - DEVE DAR ERRO
            'tipo_evento' => 'presencial',
            'tipo_pagamento' => 'gratis',
            'vagas' => 50,
        ]);

        $response->assertSessionHasErrors(['data_fim_inscricao']);
    }

    /** @test */
    public function detalhes_pagamento_is_required_when_tipo_pagamento_is_outros()
    {
        $user = User::factory()->create(['tipo_usuario' => 'admin']);

        $response = $this->actingAs($user)->post('app/eventos', [
            'nome' => 'Evento Teste',
            'descricao' => 'Descrição válida',
            'tipo_classificacao' => 'Workshop',
            'area_tematica' => 'Tecnologia',
            'data_inicio_evento' => '2024-03-01 10:00:00',
            'data_fim_evento' => '2024-03-01 12:00:00',
            'data_inicio_inscricao' => '2024-02-01 00:00:00',
            'data_fim_inscricao' => '2024-02-28 23:59:00',
            'tipo_evento' => 'presencial',
            'tipo_pagamento' => 'outros', // Tipo que exige detalhes
            'detalhes_pagamento' => '',   // Campo vazio - DEVE DAR ERRO
            'vagas' => 50,
        ]);

        $response->assertSessionHasErrors(['detalhes_pagamento']);
    }

    /** @test */
    public function logomarca_must_be_valid_image()
    {
        $user = User::factory()->create(['tipo_usuario' => 'admin']);
        $invalidFile = UploadedFile::fake()->create('documento.pdf', 1000); 

        $response = $this->actingAs($user)->post('app/eventos', [
            'nome' => 'Evento Teste',
            'descricao' => 'Descrição válida',
            'tipo_classificacao' => 'Workshop',
            'area_tematica' => 'Tecnologia',
            'data_inicio_evento' => '2024-03-01 10:00:00',
            'data_fim_evento' => '2024-03-01 12:00:00',
            'data_inicio_inscricao' => '2024-02-01 00:00:00',
            'data_fim_inscricao' => '2024-02-28 23:59:00',
            'tipo_evento' => 'presencial',
            'tipo_pagamento' => 'gratis',
            'logomarca' => $invalidFile, // Arquivo inválido - DEVE DAR ERRO
            'vagas' => 50,
        ]);

        $response->assertSessionHasErrors(['logomarca']);
    }

    /** @test */
    public function vagas_must_be_positive_integer()
    {
        $user = User::factory()->create(['tipo_usuario' => 'admin']);

        $response = $this->actingAs($user)->post('app/eventos', [
            'nome' => 'Evento Teste',
            'descricao' => 'Descrição válida',
            'tipo_classificacao' => 'Workshop',
            'area_tematica' => 'Tecnologia',
            'data_inicio_evento' => '2024-03-01 10:00:00',
            'data_fim_evento' => '2024-03-01 12:00:00',
            'data_inicio_inscricao' => '2024-02-01 00:00:00',
            'data_fim_inscricao' => '2024-02-28 23:59:00',
            'tipo_evento' => 'presencial',
            'tipo_pagamento' => 'gratis',
            'vagas' => -10, // Número negativo - DEVE DAR ERRO
        ]);

        $response->assertSessionHasErrors(['vagas']);
    }

    /** @test */
    public function successful_event_creation()
    {
        $user = User::factory()->create(['tipo_usuario' => 'admin']);

        $response = $this->actingAs($user)->post('app/eventos', [
            'nome' => 'Evento Válido',
            'descricao' => 'Descrição válida',
            'tipo_classificacao' => 'Workshop',
            'area_tematica' => 'Tecnologia',
            'data_inicio_evento' => '2024-03-01 10:00:00',
            'data_fim_evento' => '2024-03-01 12:00:00',
            'data_inicio_inscricao' => '2024-02-01 00:00:00',
            'data_fim_inscricao' => '2024-02-28 23:59:00',
            'tipo_evento' => 'presencial',
            'tipo_pagamento' => 'gratis',
            'vagas' => 50,
        ]);

        // Se criar com sucesso, deve redirecionar
        $response->assertRedirect();
    }
}