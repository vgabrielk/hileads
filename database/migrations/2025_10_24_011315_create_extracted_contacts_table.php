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
        Schema::create('extracted_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('whatsapp_group_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('contact_name')->nullable();
            $table->string('contact_picture')->nullable();
            $table->boolean('is_contacted')->default(false);
            $table->timestamp('contacted_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('new'); // new, contacted, interested, not_interested, converted
            $table->timestamps();
            
            $table->unique(['user_id', 'phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extracted_contacts');
    }
};
