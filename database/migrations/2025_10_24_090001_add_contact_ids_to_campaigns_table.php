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
        // Check if the column already exists before adding it
        if (!Schema::hasColumn('mass_sendings', 'contact_ids')) {
            Schema::table('mass_sendings', function (Blueprint $table) {
                $table->json('contact_ids')->nullable()->after('message');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the column exists before dropping it
        if (Schema::hasColumn('mass_sendings', 'contact_ids')) {
            Schema::table('mass_sendings', function (Blueprint $table) {
                $table->dropColumn('contact_ids');
            });
        }
    }
};
