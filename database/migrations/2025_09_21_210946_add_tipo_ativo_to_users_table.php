<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cria a coluna tipo_usuario se ainda não existir
            if (! Schema::hasColumn('users', 'tipo_usuario')) {
                $table->string('tipo_usuario')
                    ->default('comum')
                    ->after('password');
            }

            // Cria a coluna ativo se ainda não existir
            if (! Schema::hasColumn('users', 'ativo')) {
                $table->boolean('ativo')
                    ->default(true)
                    ->after('tipo_usuario');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ativo')) {
                $table->dropColumn('ativo');
            }
            if (Schema::hasColumn('users', 'tipo_usuario')) {
                $table->dropColumn('tipo_usuario');
            }
        });
    }
};
