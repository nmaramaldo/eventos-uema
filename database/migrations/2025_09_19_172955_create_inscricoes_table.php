<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('evento_id')->constrained('eventos');
            $table->string('status');
            $table->timestamp('data_inscricao')->useCurrent();
            $table->boolean('presente')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'evento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};