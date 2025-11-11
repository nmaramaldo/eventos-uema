<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Coluna para o tipo de pagamento (gratis, pix, outros)
            $table->string('tipo_pagamento')->default('gratis')->after('vagas');
            // Coluna para os detalhes do "outros" (ex: link, conta bancária)
            $table->text('detalhes_pagamento')->nullable()->after('tipo_pagamento');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['tipo_pagamento', 'detalhes_pagamento']);
        });
    }
};