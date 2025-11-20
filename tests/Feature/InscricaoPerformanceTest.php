<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Inscricao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscricaoPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_handle_multiple_inscricoes_in_sequence()
    {
        $event = Event::factory()->create([
            'vagas' => 1000,
            'data_inicio_inscricao' => Carbon::now()->subDay(),
            'data_fim_inscricao' => Carbon::now()->addDay(),
            'status' => 'ativo'
        ]);

        $users = User::factory()->count(50)->create();

        $startTime = microtime(true);

        $successfulInscricoes = 0;

        foreach ($users as $user) {
            $response = $this->actingAs($user)
                ->post('/app/inscricoes', ['evento_id' => $event->id]);

            if ($response->getStatusCode() === 302) {
                $successfulInscricoes++;
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $actualInscricoes = Inscricao::count();

        echo "Performance: {$actualInscricoes} inscrições criadas em {$executionTime} segundos\n";
        echo "Tentativas: 50, Sucessos: {$successfulInscricoes}, Criadas: {$actualInscricoes}\n";

        $this->assertLessThan(
            15,
            $executionTime,
            "50 tentativas de inscrição levaram {$executionTime}s - muito lento!"
        );

        $this->assertGreaterThan(
            0,
            $actualInscricoes,
            "Nenhuma inscrição foi criada - verificar regras de negócio!"
        );
    }

    /** @test */
    public function can_handle_large_events_with_thousands_of_vagas()
    {
        $event = Event::factory()->create([
            'nome' => 'Mega Evento',
            'vagas' => 5000,
            'data_inicio_inscricao' => Carbon::now()->subDay(),
            'data_fim_inscricao' => Carbon::now()->addDay(),
            'status' => 'ativo'
        ]);

        $response = $this->get("/app/eventos/{$event->id}");

        if ($response->getStatusCode() === 200) {
            $response->assertSee('Mega Evento');
            $response->assertSee('5000');
        } else {
            $redirectResponse = $this->followRedirects($response);
            $redirectResponse->assertStatus(200);
        }

        $users = User::factory()->count(20)->create();
        $createdInscricoes = 0;

        foreach ($users as $user) {
            $response = $this->actingAs($user)
                ->post('/app/inscricoes', ['evento_id' => $event->id]);

            if ($response->getStatusCode() === 302) {
                $createdInscricoes++;
            }
        }

        echo "Inscrições criadas no mega evento: {$createdInscricoes} de 20 tentativas\n";

        $this->assertGreaterThan(
            0,
            $createdInscricoes,
            "Nenhuma inscrição foi criada no evento de 5000 vagas!"
        );
    }

    /** @test */
    public function database_performance_with_medium_datasets()
    {
        Event::factory()->count(50)->create();

        $user = User::factory()->create(['tipo_usuario' => 'admin']);

        $startTime = microtime(true);

        $response = $this->actingAs($user)
            ->get('/app/eventos');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        if ($response->getStatusCode() === 200) {
            $response->assertStatus(200);
        } else {
            echo "Listagem retornou status: {$response->getStatusCode()}\n";
        }

        $this->assertLessThan(
            3,
            $executionTime,
            "Listagem de 50 eventos levou {$executionTime}s - considerar otimização!"
        );

        echo "Performance: Listagem de 50 eventos em {$executionTime} segundos\n";
        echo "Status: {$response->getStatusCode()}\n";
    }

    /** @test */
    public function system_handles_rapid_page_requests()
    {
        $event = Event::factory()->create([
            'status' => 'ativo'
        ]);

        $user = User::factory()->create(['tipo_usuario' => 'master']);


        $startTime = microtime(true);

        $successfulRequests = 0;

        for ($i = 0; $i < 10; $i++) {

            // requisita a página
            $response = $this->actingAs($user)
                ->get("/app/eventos/{$event->id}");

            // segue qualquer redirect
            $finalResponse = $this->followRedirects($response);

            // conta sucesso somente se a página final renderizar
            if ($finalResponse->getStatusCode() === 200) {
                $successfulRequests++;
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "Requisições bem-sucedidas: {$successfulRequests} de 10\n";
        echo "Tempo total: {$executionTime} segundos\n";

        $this->assertGreaterThan(
            5,
            $successfulRequests,
            "Muitas requisições falharam - verificar estabilidade!"
        );

        $this->assertLessThan(
            5,
            $executionTime,
            "10 requisições levaram {$executionTime}s - muito lento!"
        );
    }
}
