<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // foreignUuid já cria a coluna do tipo certo e a chave estrangeira
            $table->foreignUuid('coordenador_id')->nullable()->constrained('users'); // <-- CORRIGIDO: Adicionado nullable

            $table->string('nome');
            $table->text('descricao')->nullable(); // <-- CORRIGIDO: Adicionado nullable

            // Adicionando as colunas que estavam faltando na criação original
            $table->string('tipo_classificacao')->nullable(); // <-- CORRIGIDO: Adicionado nullable
            $table->string('local_principal')->nullable();      // <-- CORRIGIDO: Adicionado nullable

            // Corrigindo os tipos de dados de DATE para DATETIME para incluir a hora
            $table->dateTime('data_inicio_evento');      // <-- CORRIGIDO: Tipo de dado
            $table->dateTime('data_fim_evento');         // <-- CORRIGIDO: Tipo de dado
            $table->dateTime('data_inicio_inscricao');   // <-- CORRIGIDO: Tipo de dado
            $table->dateTime('data_fim_inscricao');      // <-- CORRIGIDO: Tipo de dado
            
            $table->string('tipo_evento');
            $table->string('logomarca')->nullable();
            
            // Corrigindo o status para ter um valor padrão
            $table->string('status')->default('rascunho'); // <-- CORRIGIDO: Adicionado default
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};