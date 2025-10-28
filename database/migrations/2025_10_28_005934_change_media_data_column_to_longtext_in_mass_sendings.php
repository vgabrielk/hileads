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
        Schema::table('mass_sendings', function (Blueprint $table) {
            // Mudar de JSON para LONGTEXT para suportar dados maiores
            $table->longText('media_data')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mass_sendings', function (Blueprint $table) {
            // Voltar para JSON
            $table->json('media_data')->nullable()->change();
        });
    }
};