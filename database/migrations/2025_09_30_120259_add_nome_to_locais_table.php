<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('locais','nome')) {
            Schema::table('locais', function (Blueprint $table) {
                $table->string('nome')->after('id');
                $table->index('nome');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('locais','nome')) {
            Schema::table('locais', function (Blueprint $table) {
                $table->dropIndex(['nome']);
                $table->dropColumn('nome');
            });
        }
    }
};
