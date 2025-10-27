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
        // Check if campaigns table exists and add column if needed
        if (Schema::hasTable('campaigns') && !Schema::hasColumn('campaigns', 'contact_ids')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->json('contact_ids')->nullable()->after('message');
            });
        }
        
        // Check if mass_sendings table exists and add column if needed
        if (Schema::hasTable('mass_sendings') && !Schema::hasColumn('mass_sendings', 'contact_ids')) {
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
        // Check if campaigns table exists and drop column if needed
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'contact_ids')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('contact_ids');
            });
        }
        
        // Check if mass_sendings table exists and drop column if needed
        if (Schema::hasTable('mass_sendings') && Schema::hasColumn('mass_sendings', 'contact_ids')) {
            Schema::table('mass_sendings', function (Blueprint $table) {
                $table->dropColumn('contact_ids');
            });
        }
    }
};
