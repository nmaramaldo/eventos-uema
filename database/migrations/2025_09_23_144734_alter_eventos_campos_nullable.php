<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Cria uma nova tabela com os campos que precisam ser nullable
        Schema::create('eventos_new', function (Blueprint $table) {
            $table->string('id')->primary();               // UUID
            $table->string('coordenador_id')->nullable();   // pode ser nulo
            $table->string('nome');
            $table->text('descricao')->nullable();          // <-- agora nullable
            $table->string('tipo_evento');                  // presencial|online|...
            $table->string('tipo_classificacao')->nullable(); // <-- agora nullable
            $table->string('area_tematica')->nullable();      // <-- agora nullable
            $table->dateTime('data_inicio_evento');
            $table->dateTime('data_fim_evento');
            $table->dateTime('data_inicio_inscricao');
            $table->dateTime('data_fim_inscricao');
            $table->string('logomarca_url')->nullable();    // <-- agora nullable
            $table->string('status')->default('rascunho');
            $table->timestamps();
        });

        // 2) Copia os dados da antiga para a nova (colunas na MESMA ordem!)
        DB::statement('
            INSERT INTO eventos_new (
                id, coordenador_id, nome, descricao, tipo_evento,
                tipo_classificacao, area_tematica,
                data_inicio_evento, data_fim_evento,
                data_inicio_inscricao, data_fim_inscricao,
                logomarca_url, status, created_at, updated_at
            )
            SELECT
                id, coordenador_id, nome, descricao, tipo_evento,
                tipo_classificacao, area_tematica,
                data_inicio_evento, data_fim_evento,
                data_inicio_inscricao, data_fim_inscricao,
                logomarca_url, status, created_at, updated_at
            FROM eventos
        ');

        // 3) Troca as tabelas
        Schema::drop('eventos');
        Schema::rename('eventos_new', 'eventos');
    }

    public function down(): void
    {
        // Reverte para NOT NULL (se precisar). Mesma ideia ao contrário:
        Schema::create('eventos_old', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('coordenador_id')->nullable();
            $table->string('nome');
            $table->text('descricao');                      // NOT NULL novamente
            $table->string('tipo_evento');
            $table->string('tipo_classificacao');           // NOT NULL novamente
            $table->string('area_tematica');                // NOT NULL novamente
            $table->dateTime('data_inicio_evento');
            $table->dateTime('data_fim_evento');
            $table->dateTime('data_inicio_inscricao');
            $table->dateTime('data_fim_inscricao');
            $table->string('logomarca_url');                // NOT NULL novamente
            $table->string('status')->default('rascunho');
            $table->timestamps();
        });

        DB::statement('
            INSERT INTO eventos_old (
                id, coordenador_id, nome, descricao, tipo_evento,
                tipo_classificacao, area_tematica,
                data_inicio_evento, data_fim_evento,
                data_inicio_inscricao, data_fim_inscricao,
                logomarca_url, status, created_at, updated_at
            )
            SELECT
                id, coordenador_id, nome, descricao, tipo_evento,
                tipo_classificacao, area_tematica,
                data_inicio_evento, data_fim_evento,
                data_inicio_inscricao, data_fim_inscricao,
                logomarca_url, status, created_at, updated_at
            FROM eventos
        ');

        Schema::drop('eventos');
        Schema::rename('eventos_old', 'eventos');
    }
};
