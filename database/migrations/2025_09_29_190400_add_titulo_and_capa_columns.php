<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // eventos: capa_url (upload da capa)
        Schema::table('eventos', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos', 'capa_url')) {
                $table->string('capa_url')->nullable()->after('logomarca_url');
            }
        });

        // eventos_detalhes: titulo (nome da atividade)
        Schema::table('eventos_detalhes', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos_detalhes', 'titulo')) {
                $table->string('titulo')->nullable()->after('evento_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            if (Schema::hasColumn('eventos', 'capa_url')) {
                $table->dropColumn('capa_url');
            }
        });

        Schema::table('eventos_detalhes', function (Blueprint $table) {
            if (Schema::hasColumn('eventos_detalhes', 'titulo')) {
                $table->dropColumn('titulo');
            }
        });
    }
};
