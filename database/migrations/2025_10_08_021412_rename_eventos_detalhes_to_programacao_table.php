<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::rename('eventos_detalhes', 'programacao');
    }

    public function down(): void
    {
        Schema::rename('programacao', 'eventos_detalhes');
    }
};