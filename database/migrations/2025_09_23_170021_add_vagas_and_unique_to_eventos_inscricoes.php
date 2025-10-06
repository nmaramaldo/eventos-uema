<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Campo "vagas" no evento (opcional)
        Schema::table('eventos', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos', 'vagas')) {
                $table->unsignedInteger('vagas')->nullable()->after('status');
            }
        });

        // Índice único user_id + evento_id para evitar duplicidade de inscrição
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // No SQLite criamos via SQL bruto com IF NOT EXISTS
            DB::statement(
                'CREATE UNIQUE INDEX IF NOT EXISTS inscricoes_user_evento_unique ON inscricoes (user_id, evento_id)'
            );
        } else {
            // Em MySQL/Postgres usamos o schema builder
            Schema::table('inscricoes', function (Blueprint $table) {
                $table->unique(['user_id', 'evento_id'], 'inscricoes_user_evento_unique');
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS inscricoes_user_evento_unique');
        } else {
            Schema::table('inscricoes', function (Blueprint $table) {
                $table->dropUnique('inscricoes_user_evento_unique');
            });
        }

        Schema::table('eventos', function (Blueprint $table) {
            if (Schema::hasColumn('eventos', 'vagas')) {
                $table->dropColumn('vagas');
            }
        });
    }
};
