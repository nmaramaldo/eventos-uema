<?php
// ... create_eventos_detalhes_palestrantes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_detalhes_palestrantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eventos_detalhes_id')->constrained('eventos_detalhes')->onDelete('cascade');
            $table->foreignId('palestrantes_id')->constrained('palestrantes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_detalhes_palestrantes');
    }
};