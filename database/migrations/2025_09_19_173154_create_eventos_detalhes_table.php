<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_detalhes', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Alterado para UUID
            $table->foreignUuid('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->text('descricao');
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->string('modalidade');
            $table->integer('capacidade');
            $table->string('localidade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_detalhes');
    }
};