<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('locais', function (Blueprint $table) {
            if (!Schema::hasColumn('locais', 'nome')) {
                $table->string('nome')->after('id');
            }
            if (!Schema::hasColumn('locais', 'tipo')) {
                $table->string('tipo', 30)->nullable();
            }
            if (!Schema::hasColumn('locais', 'campus')) {
                $table->string('campus', 120)->nullable();
            }
            if (!Schema::hasColumn('locais', 'predio')) {
                $table->string('predio', 120)->nullable();
            }
            if (!Schema::hasColumn('locais', 'sala')) {
                $table->string('sala', 80)->nullable();
            }
            if (!Schema::hasColumn('locais', 'capacidade')) {
                $table->unsignedInteger('capacidade')->nullable();
            }
            if (!Schema::hasColumn('locais', 'observacoes')) {
                $table->text('observacoes')->nullable();
            }
            // Se no futuro você quiser amarrar local ao evento:
            // if (!Schema::hasColumn('locais','evento_id')) {
            //     $table->uuid('evento_id')->nullable()->index();
            // }
        });
    }

    public function down(): void
    {
        Schema::table('locais', function (Blueprint $table) {
            foreach (['observacoes','capacidade','sala','predio','campus','tipo','nome'] as $col) {
                if (Schema::hasColumn('locais', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
