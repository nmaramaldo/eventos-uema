<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programacao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('evento_id')->constrained('eventos')->cascadeOnDelete();
            
            // CAMPOS PRINCIPAIS 
            $table->string('titulo');
            $table->text('descricao')->nullable();
            
            // DATETIMES 
            $table->datetime('data_hora_inicio');
            $table->datetime('data_hora_fim');
            
            // INFORMAÇÕES DA ATIVIDADE 
            $table->string('modalidade')->default('Presencial');
            $table->integer('capacidade')->nullable();
            $table->string('localidade')->nullable(); // Permitindo nulo para atividades online

            // CONTROLE DE INSCRIÇÕES
            $table->boolean('requer_inscricao')->default(false);

            // AUDITORIA
            $table->timestamps();
            
            // INDEXES PARA PERFORMANCE
            $table->index(['evento_id', 'data_hora_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programacao');
    }
};