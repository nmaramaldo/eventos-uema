<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('programacao_palestrante')) {
            Schema::create('programacao_palestrante', function (Blueprint $table) {
                $table->uuid('programacao_id');
                $table->uuid('palestrante_id');
                $table->timestamps();

                $table->primary(['programacao_id', 'palestrante_id']);
                // (opcional) crie FKs se desejar e seu BD suportar:
                // $table->foreign('programacao_id')->references('id')->on('programacao')->cascadeOnDelete();
                // $table->foreign('palestrante_id')->references('id')->on('palestrantes')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('programacao_palestrante');
    }
};
