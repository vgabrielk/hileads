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
        Schema::create('whatsapp_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('whatsapp_connection_id')->constrained()->onDelete('cascade');
            $table->string('group_id')->unique();
            $table->string('group_name');
            $table->text('group_description')->nullable();
            $table->string('group_picture')->nullable();
            $table->integer('participants_count')->default(0);
            $table->boolean('is_extracted')->default(false);
            $table->timestamp('last_extraction')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_groups');
    }
};
