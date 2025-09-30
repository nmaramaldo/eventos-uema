<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('evento_detalhes')) {
            // Se sua tabela tiver outro nome, ajuste aqui.
            return;
        }

        Schema::table('evento_detalhes', function (Blueprint $table) {
            if (!Schema::hasColumn('evento_detalhes', 'local_id')) {
                $table->uuid('local_id')->nullable()->after('evento_id');
                $table->index('local_id', 'evento_detalhes_local_id_index');
            }
        });

        // FK só em bancos que não sejam SQLite (para evitar dor de cabeça)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'sqlite') {
            Schema::table('evento_detalhes', function (Blueprint $table) {
                $table->foreign('local_id')
                      ->references('id')->on('locais')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('evento_detalhes')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'sqlite') {
            Schema::table('evento_detalhes', function (Blueprint $table) {
                $table->dropForeign(['local_id']);
            });
        }

        Schema::table('evento_detalhes', function (Blueprint $table) {
            if (Schema::hasColumn('evento_detalhes', 'local_id')) {
                $table->dropIndex('evento_detalhes_local_id_index');
                $table->dropColumn('local_id');
            }
        });
    }
};
