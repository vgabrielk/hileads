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
        Schema::table('campaigns', function (Blueprint $table) {
            // Add composite index for efficient user campaigns query
            $table->index(['user_id', 'created_at'], 'campaigns_user_created_index');
            
            // Add index for status filtering
            $table->index('status', 'campaigns_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex('campaigns_user_created_index');
            $table->dropIndex('campaigns_status_index');
        });
    }
};