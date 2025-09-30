<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Adiciona APENAS se ainda não existir (evita erro ao rodar mais de uma vez)
            if (!Schema::hasColumn('eventos', 'tipo_classificacao')) {
                // depois de 'tipo_evento' só por organização; pode remover o ->after() se quiser
                $table->string('tipo_classificacao', 255)->nullable()->after('tipo_evento');
            }

            if (!Schema::hasColumn('eventos', 'area_tematica')) {
                $table->string('area_tematica', 255)->nullable()->after('tipo_classificacao');
            }
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            if (Schema::hasColumn('eventos', 'area_tematica')) {
                $table->dropColumn('area_tematica');
            }
            if (Schema::hasColumn('eventos', 'tipo_classificacao')) {
                $table->dropColumn('tipo_classificacao');
            }
        });
    }
};

