<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('usuario_id')->constrained('usuarios');
            $table->string('titulo');
            $table->text('mensagem');
            $table->timestamp('enviado_em');
            $table->boolean('lido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};