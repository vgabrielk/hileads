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
        Schema::create('sent_messages', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_type'); // 'campaign' or 'mass_sending'
            $table->unsignedBigInteger('campaign_id');
            $table->string('phone_number');
            $table->string('jid')->nullable();
            $table->string('message_id')->nullable();
            $table->enum('status', ['sent', 'delivered', 'failed'])->default('sent');
            $table->timestamp('sent_at');
            $table->json('response_data')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['campaign_type', 'campaign_id', 'phone_number']);
            $table->index(['phone_number']);
            $table->index(['sent_at']);
            
            // Unique constraint to prevent duplicates
            $table->unique(['campaign_type', 'campaign_id', 'phone_number'], 'unique_campaign_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_messages');
    }
};
