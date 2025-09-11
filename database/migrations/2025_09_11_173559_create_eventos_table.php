<?php
// ... create_eventos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('coordenador_id')->constrained('users');
            $table->string('nome');
            $table->text('descricao');
            $table->date('data_inicio_evento');
            $table->date('data_fim_evento');
            $table->date('data_inicio_inscricao');
            $table->date('data_fim_inscricao');
            $table->string('tipo_evento');
            $table->text('logomarca_url')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
