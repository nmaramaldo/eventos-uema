<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Adiciona a coluna 'cpf' após a coluna 'email'
            $table->string('cpf', 14)->unique()->after('email');
        });
    }

    /**
     * Reverte as migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Remove a coluna 'cpf' se a migration for revertida
            $table->dropColumn('cpf');
        });
    }
};