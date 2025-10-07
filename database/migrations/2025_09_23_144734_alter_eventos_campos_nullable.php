<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Garante idempotência: se sobrou de tentativa anterior, apaga
        Schema::dropIfExists('eventos_new');

        // 1) Cria a nova tabela com os campos que precisam ser nullable
        Schema::create('eventos_new', function (Blueprint $table) {
            $table->string('id')->primary();                 // UUID em string
            $table->string('coordenador_id')->nullable();    // pode ser nulo
            $table->string('nome');
            $table->text('descricao')->nullable();           // <-- agora nullable
            $table->string('tipo_evento');                   // presencial|online|...
            $table->string('tipo_classificacao')->nullable();// <-- agora nullable
            $table->string('area_tematica')->nullable();     // <-- agora nullable
            $table->dateTime('data_inicio_evento');
            $table->dateTime('data_fim_evento');
            $table->dateTime('data_inicio_inscricao');
            $table->dateTime('data_fim_inscricao');
            $table->string('logomarca_url')->nullable();     // <-- agora nullable
            $table->string('status')->default('rascunho');
            $table->timestamps();
        });

        // 2) Copia os dados da antiga para a nova (se a antiga existir)
        if (Schema::hasTable('eventos')) {
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
        }

        // 3) Troca as tabelas de forma segura (compatível com SQLite)
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('eventos');              // nada de CASCADE cru aqui
        Schema::rename('eventos_new', 'eventos');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Idempotência no down
        Schema::dropIfExists('eventos_old');

        // Recria versão antiga com NOT NULL onde era obrigatório
        Schema::create('eventos_old', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('coordenador_id')->nullable();
            $table->string('nome');
            $table->text('descricao');                        // NOT NULL novamente
            $table->string('tipo_evento');
            $table->string('tipo_classificacao');             // NOT NULL novamente
            $table->string('area_tematica');                  // NOT NULL novamente
            $table->dateTime('data_inicio_evento');
            $table->dateTime('data_fim_evento');
            $table->dateTime('data_inicio_inscricao');
            $table->dateTime('data_fim_inscricao');
            $table->string('logomarca_url');                  // NOT NULL novamente
            $table->string('status')->default('rascunho');
            $table->timestamps();
        });

        if (Schema::hasTable('eventos')) {
            // COALESCE para evitar violar NOT NULL ao reverter
            DB::statement("
                INSERT INTO eventos_old (
                    id, coordenador_id, nome, descricao, tipo_evento,
                    tipo_classificacao, area_tematica,
                    data_inicio_evento, data_fim_evento,
                    data_inicio_inscricao, data_fim_inscricao,
                    logomarca_url, status, created_at, updated_at
                )
                SELECT
                    id, coordenador_id, nome,
                    COALESCE(descricao, ''),
                    tipo_evento,
                    COALESCE(tipo_classificacao, ''),
                    COALESCE(area_tematica, ''),
                    data_inicio_evento, data_fim_evento,
                    data_inicio_inscricao, data_fim_inscricao,
                    COALESCE(logomarca_url, ''),
                    status, created_at, updated_at
                FROM eventos
            ");
        }

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('eventos');
        Schema::rename('eventos_old', 'eventos');
        Schema::enableForeignKeyConstraints();
    }
};
