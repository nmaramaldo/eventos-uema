<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            // nomeie o índice para facilitar rollback
            $table->unique(['user_id','evento_id'], 'inscricoes_user_event_unique');
        });
    }

    public function down(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->dropUnique('inscricoes_user_event_unique');
        });
    }
};
