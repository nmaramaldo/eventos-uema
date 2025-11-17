<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            // todas como nullable para não quebrar dados antigos
            if (!Schema::hasColumn('certificados', 'modelo_id')) {
                $table->uuid('modelo_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('certificados', 'tipo')) {
                $table->string('tipo', 50)->nullable()->after('modelo_id');
            }

            if (!Schema::hasColumn('certificados', 'hash_verificacao')) {
                $table->string('hash_verificacao', 64)->nullable()->unique()->after('tipo');
            }

            // se quiser e a tabela ainda não tiver, podemos amarrar direto ao usuário
            if (!Schema::hasColumn('certificados', 'user_id')) {
                $table->uuid('user_id')->nullable()->after('hash_verificacao');
            }

            // FK simples (SQLite ignora algumas constraints, mas não quebra)
            // use try/catch se seu banco reclamar, mas normalmente ok.
        });
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            if (Schema::hasColumn('certificados', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('certificados', 'hash_verificacao')) {
                $table->dropColumn('hash_verificacao');
            }
            if (Schema::hasColumn('certificados', 'tipo')) {
                $table->dropColumn('tipo');
            }
            if (Schema::hasColumn('certificados', 'modelo_id')) {
                $table->dropColumn('modelo_id');
            }
        });
    }
};
