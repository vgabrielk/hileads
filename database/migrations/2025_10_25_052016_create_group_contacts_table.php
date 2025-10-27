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
        Schema::create('group_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->string('contact_jid'); // JID do contato da API Wuzapi
            $table->string('contact_name')->nullable(); // Nome do contato para referência
            $table->string('contact_phone'); // Telefone do contato
            $table->timestamps();

            $table->unique(['group_id', 'contact_jid']);
            $table->index(['group_id', 'contact_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_contacts');
    }
};
