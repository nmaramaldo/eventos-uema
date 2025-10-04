<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');                 // "Auditório CCT", "Sala 101", "Campus Paulo VI"
            $table->string('tipo', 30)->nullable(); // 'auditorio','sala','laboratorio','predio','campus','outro'
            $table->string('campus', 120)->nullable(); // ex.: "Paulo VI"
            $table->string('predio', 120)->nullable(); // ex.: "CCT", "CCA", "CECEN", "CCS"
            $table->string('sala', 80)->nullable();    // ex.: "Sala 101"
            $table->unsignedInteger('capacidade')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locais');
    }
};
