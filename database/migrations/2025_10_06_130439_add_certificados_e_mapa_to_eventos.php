<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Certificado
            $table->string('certificado_bg_path')->nullable();
            $table->unsignedInteger('certificado_nome_x')->nullable();
            $table->unsignedInteger('certificado_nome_y')->nullable();
            $table->unsignedInteger('certificado_font_size')->nullable()->default(48);
            $table->string('certificado_text_color', 9)->nullable()->default('#000000');

            // Local + Mapa (pedido)
            $table->string('local_principal')->nullable();
            $table->string('mapa_evento_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn([
                'certificado_bg_path',
                'certificado_nome_x',
                'certificado_nome_y',
                'certificado_font_size',
                'certificado_text_color',
                'local_principal',
                'mapa_evento_path',
            ]);
        });
    }
};
