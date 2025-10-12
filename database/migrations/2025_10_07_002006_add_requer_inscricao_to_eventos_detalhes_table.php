<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programacao', function (Blueprint $table) {
            // adiciona a coluna apenas se ela ainda não existir
            if (! Schema::hasColumn('programacao', 'requer_inscricao')) {
                // em SQLite, boolean vira INTEGER (0/1) — tudo bem
                $table->boolean('requer_inscricao')->default(false)->after('capacidade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programacao', function (Blueprint $table) {
            if (Schema::hasColumn('programacao', 'requer_inscricao')) {
                $table->dropColumn('requer_inscricao');
            }
        });
    }
};
