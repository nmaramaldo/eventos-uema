<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('eventos_new');

        // 1) Cria nova tabela com UUID
        Schema::create('eventos_new', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('coordenador_id')->nullable();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('tipo_evento');
            $table->string('tipo_classificacao')->nullable();
            $table->string('local_principal')->nullable();
            $table->dateTime('data_inicio_evento');
            $table->dateTime('data_fim_evento');
            $table->dateTime('data_inicio_inscricao');
            $table->dateTime('data_fim_inscricao');
            $table->string('logomarca')->nullable();
            $table->string('status')->default('rascunho');
            $table->timestamps();
        });

        Schema::disableForeignKeyConstraints();
        
        $this->dropForeignIfExists('inscricoes', 'inscricoes_evento_id_foreign');
        $this->dropForeignIfExists('programacao', 'programacao_evento_id_foreign');
        $this->dropForeignIfExists('evento_palestrante', 'evento_palestrante_evento_id_foreign');

        // 3) Copia dados - abordagem mais simples
        if (Schema::hasTable('eventos')) {
            // Verifica se a coluna id atual é string (varchar)
            $columnType = DB::selectOne("
                SELECT data_type 
                FROM information_schema.columns 
                WHERE table_name = 'eventos' AND column_name = 'id'
            ");

            if ($columnType->data_type === 'character varying') {
                // Se é string, tenta converter para UUID
                DB::statement('
                    INSERT INTO eventos_new (
                        id, coordenador_id, nome, descricao, tipo_evento,
                        tipo_classificacao, local_principal,
                        data_inicio_evento, data_fim_evento,
                        data_inicio_inscricao, data_fim_inscricao,
                        logomarca, status, created_at, updated_at
                    )
                    SELECT
                        CASE 
                            WHEN id ~ ? THEN id::uuid
                            ELSE gen_random_uuid()
                        END,
                        CASE 
                            WHEN coordenador_id IS NOT NULL AND coordenador_id ~ ? THEN coordenador_id::uuid
                            ELSE NULL 
                        END,
                        nome, descricao, tipo_evento,
                        tipo_classificacao, local_principal,
                        data_inicio_evento, data_fim_evento,
                        data_inicio_inscricao, data_fim_inscricao,
                        logomarca, status, created_at, updated_at
                    FROM eventos
                ', [
                    '^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$',
                    '^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$'
                ]);
            } else {
                // Se já é UUID, copia diretamente
                DB::statement('
                    INSERT INTO eventos_new (
                        id, coordenador_id, nome, descricao, tipo_evento,
                        tipo_classificacao, local_principal,
                        data_inicio_evento, data_fim_evento,
                        data_inicio_inscricao, data_fim_inscricao,
                        logomarca, status, created_at, updated_at
                    )
                    SELECT
                        id,
                        coordenador_id,
                        nome, descricao, tipo_evento,
                        tipo_classificacao, local_principal,
                        data_inicio_evento, data_fim_evento,
                        data_inicio_inscricao, data_fim_inscricao,
                        logomarca, status, created_at, updated_at
                    FROM eventos
                ');
            }
        }

        Schema::dropIfExists('eventos');
        Schema::rename('eventos_new', 'eventos');

        // Converte colunas dependentes para UUID
        $this->convertColumnToUUID('inscricoes', 'evento_id');
        $this->convertColumnToUUID('programacao', 'evento_id');
        $this->convertColumnToUUID('evento_palestrante', 'evento_id');

        // Recria as FKs
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->foreign('evento_id')->references('id')->on('eventos');
        });
        
        Schema::table('programacao', function (Blueprint $table) {
            $table->foreign('evento_id')->references('id')->on('eventos');
        });
        
        Schema::table('evento_palestrante', function (Blueprint $table) {
            $table->foreign('evento_id')->references('id')->on('eventos');
        });
        
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_old');

        // Cria tabela antiga com string
        Schema::create('eventos_old', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('coordenador_id')->nullable();
            $table->string('nome');
            $table->text('descricao');
            $table->string('tipo_evento');
            $table->string('tipo_classificacao');
            $table->string('local_principal');
            $table->dateTime('data_inicio_evento');
            $table->dateTime('data_fim_evento');
            $table->dateTime('data_inicio_inscricao');
            $table->dateTime('data_fim_inscricao');
            $table->string('logomarca');
            $table->string('status')->default('rascunho');
            $table->timestamps();
        });

        Schema::disableForeignKeyConstraints();
        
        $this->dropForeignIfExists('inscricoes', 'inscricoes_evento_id_foreign');
        $this->dropForeignIfExists('programacao', 'programacao_evento_id_foreign');
        $this->dropForeignIfExists('evento_palestrante', 'evento_palestrante_evento_id_foreign');

        // Converte UUIDs de volta para string
        $this->convertColumnToString('inscricoes', 'evento_id');
        $this->convertColumnToString('programacao', 'evento_id');
        $this->convertColumnToString('evento_palestrante', 'evento_id');

        // Copia dados convertendo UUID para string
        if (Schema::hasTable('eventos')) {
            DB::statement('
                INSERT INTO eventos_old (
                    id, coordenador_id, nome, descricao, tipo_evento,
                    tipo_classificacao, local_principal,
                    data_inicio_evento, data_fim_evento,
                    data_inicio_inscricao, data_fim_inscricao,
                    logomarca, status, created_at, updated_at
                )
                SELECT
                    id::text,
                    coordenador_id::text,
                    nome,
                    COALESCE(descricao, \'\'),
                    tipo_evento,
                    COALESCE(tipo_classificacao, \'\'),
                    COALESCE(local_principal, \'\'),
                    data_inicio_evento, data_fim_evento,
                    data_inicio_inscricao, data_fim_inscricao,
                    COALESCE(logomarca, \'\'),
                    status, created_at, updated_at
                FROM eventos
            ');
        }

        Schema::dropIfExists('eventos');
        Schema::rename('eventos_old', 'eventos');

        // Recria FKs
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->foreign('evento_id')->references('id')->on('eventos');
        });
        
        Schema::table('programacao', function (Blueprint $table) {
            $table->foreign('evento_id')->references('id')->on('eventos');
        });
        
        Schema::table('evento_palestrante', function (Blueprint $table) {
            $table->foreign('evento_id')->references('id')->on('eventos');
        });
        
        Schema::enableForeignKeyConstraints();
    }

    private function dropForeignIfExists(string $table, string $foreignKey): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        } catch (Exception $e) {
            // Ignora se não existir
        }
    }

    private function convertColumnToUUID(string $table, string $column): void
    {
        try {
            DB::statement("ALTER TABLE {$table} ALTER COLUMN {$column} TYPE UUID USING {$column}::uuid");
        } catch (Exception $e) {
            // Se falhar, tenta uma abordagem alternativa
            DB::statement("
                ALTER TABLE {$table} 
                ALTER COLUMN {$column} TYPE UUID 
                USING CASE 
                    WHEN {$column}::text ~ '^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$' 
                    THEN {$column}::uuid 
                    ELSE gen_random_uuid() 
                END
            ");
        }
    }

    private function convertColumnToString(string $table, string $column): void
    {
        DB::statement("ALTER TABLE {$table} ALTER COLUMN {$column} TYPE VARCHAR(255)");
    }
};