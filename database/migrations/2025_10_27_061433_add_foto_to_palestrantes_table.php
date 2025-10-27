<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('palestrantes', function (Blueprint $table) {
            if (!Schema::hasColumn('palestrantes', 'foto')) {
                $table->string('foto')->nullable()->after('biografia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('palestrantes', function (Blueprint $table) {
            if (Schema::hasColumn('palestrantes', 'foto')) {
                $table->dropColumn('foto');
            }
        });
    }
};
