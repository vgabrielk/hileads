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
        // Optimize text columns for better performance
        Schema::table('mass_sendings', function (Blueprint $table) {
            // Change long text columns to more appropriate types
            $table->text('message')->change();
            $table->text('notes')->nullable()->change();
        });

        Schema::table('extracted_contacts', function (Blueprint $table) {
            // Add length limits for better indexing
            $table->string('contact_name', 255)->nullable()->change();
            $table->string('phone_number', 20)->change();
        });

        Schema::table('whatsapp_groups', function (Blueprint $table) {
            $table->string('group_name', 255)->change();
            $table->text('group_description')->nullable()->change();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('sent_messages', function (Blueprint $table) {
            $table->text('message')->change();
            $table->string('phone_number', 20)->change();
        });

        // Add constraints for data integrity
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('email', 255)->change();
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert text column changes
        Schema::table('mass_sendings', function (Blueprint $table) {
            $table->longText('message')->change();
            $table->longText('notes')->nullable()->change();
        });

        Schema::table('extracted_contacts', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->change();
            $table->string('phone_number')->change();
        });

        Schema::table('whatsapp_groups', function (Blueprint $table) {
            $table->string('group_name')->change();
            $table->longText('group_description')->nullable()->change();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->string('name')->change();
            $table->longText('description')->nullable()->change();
        });

        Schema::table('sent_messages', function (Blueprint $table) {
            $table->longText('message')->change();
            $table->string('phone_number')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('email')->change();
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->string('name')->change();
            $table->longText('description')->nullable()->change();
        });
    }
};
