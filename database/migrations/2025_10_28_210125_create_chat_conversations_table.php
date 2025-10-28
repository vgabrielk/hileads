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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('extracted_contact_id')->nullable()->constrained('extracted_contacts')->onDelete('set null');
            $table->string('chat_jid')->unique(); // WhatsApp JID (e.g., 5511999999999@s.whatsapp.net)
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20);
            $table->text('last_message_text')->nullable();
            $table->timestamp('last_message_timestamp')->nullable();
            $table->boolean('last_message_from_me')->default(false);
            $table->integer('unread_count')->default(0);
            $table->string('avatar_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes para performance
            $table->index(['user_id', 'last_message_timestamp']);
            $table->index(['user_id', 'is_active']);
            $table->index('contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
