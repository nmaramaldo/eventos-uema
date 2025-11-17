<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificado_modelos', function (Blueprint $table) {
            // segue padrão do projeto: UUID como primary key
            $table->uuid('id')->primary();

            $table->uuid('evento_id'); // FK para eventos

            $table->string('titulo');          // Ex.: Certificado de Participação
            $table->string('slug_tipo', 100);  // Ex.: participacao_evento, atividade, organizacao
            $table->string('atribuicao', 100)->nullable();
            // Ex.: todos_inscritos_evento, inscritos_atividade, organizadores, etc.

            $table->boolean('publicado')->default(false);

            // HTML com tags {nome_participante}, {nome_evento}, etc.
            $table->longText('corpo_html');

            // Caminho da imagem de fundo (storage/public)
            $table->string('background_path')->nullable();

            $table->timestamps();

            // índice básico
            $table->index(['evento_id', 'slug_tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificado_modelos');
    }
};
