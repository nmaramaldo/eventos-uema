<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class InscricaoBusinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Illuminate\Support\Facades\Vite::partialMock()
            ->shouldReceive('__invoke')
            ->andReturn('');
    }

    /** @test */ 
    public function should_not_create_inscricao_after_inscription_period()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'data_fim_inscricao' => Carbon::now()->subDays(2)
        ]);

        $response = $this->actingAs($user)
            ->post('/app/inscricoes', ['evento_id' => $event->id]);

        // Verifica se NÃO criou a inscrição
        $this->assertDatabaseMissing('inscricoes', [
            'user_id' => $user->id,
            'evento_id' => $event->id
        ]);
    }

    /** @test */ 
    public function should_not_create_inscricao_before_inscription_period()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'data_inicio_inscricao' => Carbon::now()->addDays(2)
        ]);

        $response = $this->actingAs($user)
            ->post('/app/inscricoes', ['evento_id' => $event->id]);

        $this->assertDatabaseMissing('inscricoes', [
            'user_id' => $user->id,
            'evento_id' => $event->id
        ]);
    }

    /** @test */ 
    public function should_not_create_inscricao_when_event_is_full()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['vagas' => 1]);

        // Preenche a única vaga com outro usuário - USANDO FACTORY
        Inscricao::factory()->create([
            'user_id' => User::factory()->create()->id,
            'evento_id' => $event->id,
        ]);

        $response = $this->actingAs($user)
            ->post('/app/inscricoes', ['evento_id' => $event->id]);

        $this->assertDatabaseMissing('inscricoes', [
            'user_id' => $user->id,
            'evento_id' => $event->id
        ]);
    }

    /** @test */ 
    public function should_not_create_inscricao_twice_in_same_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        // Primeira inscrição - USANDO FACTORY
        Inscricao::factory()->create([
            'user_id' => $user->id,
            'evento_id' => $event->id,
        ]);

        $inscricoesAntes = Inscricao::where('user_id', $user->id)
                                   ->where('evento_id', $event->id)
                                   ->count();

        // Tentativa de segunda inscrição
        $response = $this->actingAs($user)
            ->post('/app/inscricoes', ['evento_id' => $event->id]);

        $inscricoesDepois = Inscricao::where('user_id', $user->id)
                                    ->where('evento_id', $event->id)
                                    ->count();

        // Deve manter apenas 1 inscrição
        $this->assertEquals(1, $inscricoesDepois);
        $this->assertEquals($inscricoesAntes, $inscricoesDepois);
    }

    /** @test */ 
    public function should_create_inscricao_when_all_conditions_are_met()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'data_inicio_inscricao' => Carbon::now()->subDays(2),
            'data_fim_inscricao' => Carbon::now()->addDays(2),
            'vagas' => 10,
            'status' => 'ativo'
        ]);

        $response = $this->actingAs($user)
            ->post('/app/inscricoes', ['evento_id' => $event->id]);

        $this->assertDatabaseHas('inscricoes', [
            'user_id' => $user->id,
            'evento_id' => $event->id
        ]);
    }

    /** @test */ 
    public function should_not_create_inscricao_in_inactive_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'status' => 'rascunho',
            'data_inicio_inscricao' => Carbon::now()->subDay(),
            'data_fim_inscricao' => Carbon::now()->addDay(),
        ]);

        $response = $this->actingAs($user)
            ->post('/app/inscricoes', ['evento_id' => $event->id]);

        $this->assertDatabaseMissing('inscricoes', [
            'user_id' => $user->id,
            'evento_id' => $event->id
        ]);
    }

    /** @test */
    public function debug_what_happens_when_validation_fails()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'data_fim_inscricao' => Carbon::now()->subDays(2) // Período expirado
        ]);

        $response = $this->actingAs($user)
            ->post('/app/inscricoes', ['evento_id' => $event->id]);

        echo "=== QUANDO VALIDAÇÃO FALHA ===\n";
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Redirect: " . ($response->isRedirect() ? 'SIM' : 'NÃO') . "\n";
        echo "Session errors: " . (session()->has('errors') ? 'SIM' : 'NÃO') . "\n";
        echo "Session success: " . (session()->get('success') ?? 'NÃO') . "\n";
        echo "Inscrição criada: " . (Inscricao::where('user_id', $user->id)->where('evento_id', $event->id)->exists() ? 'SIM' : 'NÃO') . "\n";
        
        $this->assertTrue(true);
    }
}