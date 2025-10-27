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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Preço em reais
            $table->integer('price_cents'); // Preço em centavos para API
            $table->string('interval'); // monthly, yearly
            $table->integer('interval_count')->default(1); // A cada quantos intervalos
            $table->json('features')->nullable(); // Recursos do plano
            $table->integer('max_contacts')->nullable(); // Limite de contatos
            $table->integer('max_campaigns')->nullable(); // Limite de campanhas
            $table->integer('max_mass_sendings')->nullable(); // Limite de envios em massa
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false); // Destaque do plano
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
