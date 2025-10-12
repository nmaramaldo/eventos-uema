<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // remover tabela antiga se existir
        Schema::dropIfExists('eventos_detalhes');
        
        // criar nova tabela com estrutura melhorada
        Schema::create('programacao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('evento_id')->constrained('eventos')->onDelete('cascade');
            
            // campos principais
            $table->string('titulo');
            $table->text('descricao')->nullable();
            
            // datetimes 
            $table->datetime('data_hora_inicio');
            $table->datetime('data_hora_fim');
            
            // informações da atividade
            $table->string('modalidade')->default('Presencial');
            $table->integer('capacidade')->nullable();
            $table->string('localidade');
            
            // controle de inscrições
            $table->boolean('requer_inscricao')->default(false);
            $table->integer('vagas_preenchidas')->default(0);
            
            // auditoria
            $table->timestamps();
            
            // indexes para performance
            $table->index(['evento_id', 'data_hora_inicio']);
            $table->index('data_hora_inicio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programacao');
    }
};