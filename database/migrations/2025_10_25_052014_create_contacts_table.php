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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('name')->nullable();
            $table->string('push_name')->nullable();
            $table->string('jid');
            $table->boolean('is_manual')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'phone_number']);
            $table->index(['user_id', 'jid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
