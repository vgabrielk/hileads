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
        // Rename campaigns table to mass_sendings
        Schema::rename('campaigns', 'mass_sendings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename mass_sendings table back to campaigns
        Schema::rename('mass_sendings', 'campaigns');
    }
};