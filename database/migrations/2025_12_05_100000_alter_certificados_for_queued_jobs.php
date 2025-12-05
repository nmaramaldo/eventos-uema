<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->renameColumn('url_certificado', 'path');
        });

        Schema::table('certificados', function (Blueprint $table) {
            $table->string('path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->string('path')->nullable(false)->change();
        });
        
        Schema::table('certificados', function (Blueprint $table) {
            $table->renameColumn('path', 'url_certificado');
        });
    }
};
