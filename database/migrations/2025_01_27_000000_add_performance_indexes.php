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
        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->index(['is_active', 'last_login_at'], 'idx_users_active_login');
            $table->index(['role', 'is_active'], 'idx_users_role_active');
            $table->index('api_token', 'idx_users_api_token');
        });

        // Add indexes for mass_sendings table
        Schema::table('mass_sendings', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_mass_sendings_user_status');
            $table->index(['status', 'created_at'], 'idx_mass_sendings_status_created');
            $table->index(['user_id', 'created_at'], 'idx_mass_sendings_user_created');
            $table->index('whatsapp_connection_id', 'idx_mass_sendings_connection');
        });

        // Add indexes for whatsapp_connections table
        Schema::table('whatsapp_connections', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_whatsapp_connections_user_status');
            $table->index(['status', 'last_sync'], 'idx_whatsapp_connections_status_sync');
        });

        // Add indexes for whatsapp_groups table
        Schema::table('whatsapp_groups', function (Blueprint $table) {
            $table->index(['user_id', 'whatsapp_connection_id'], 'idx_whatsapp_groups_user_connection');
            $table->index('group_id', 'idx_whatsapp_groups_group_id');
        });

        // Add indexes for extracted_contacts table
        Schema::table('extracted_contacts', function (Blueprint $table) {
            $table->index(['user_id', 'whatsapp_group_id'], 'idx_extracted_contacts_user_group');
            $table->index('phone_number', 'idx_extracted_contacts_phone');
            $table->index(['user_id', 'created_at'], 'idx_extracted_contacts_user_created');
        });

        // Add indexes for subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_subscriptions_user_status');
            $table->index(['status', 'expires_at'], 'idx_subscriptions_status_expires');
            $table->index('plan_id', 'idx_subscriptions_plan');
        });

        // Add indexes for sent_messages table
        Schema::table('sent_messages', function (Blueprint $table) {
            $table->index(['campaign_id', 'status'], 'idx_sent_messages_campaign_status');
            $table->index(['campaign_type', 'campaign_id'], 'idx_sent_messages_campaign_type_id');
            $table->index(['status', 'sent_at'], 'idx_sent_messages_status_sent_at');
        });

        // Add indexes for groups table
        Schema::table('groups', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_groups_user_created');
        });

        // Add indexes for group_contacts table
        Schema::table('group_contacts', function (Blueprint $table) {
            $table->index(['group_id', 'contact_id'], 'idx_group_contacts_group_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_active_login');
            $table->dropIndex('idx_users_role_active');
            $table->dropIndex('idx_users_api_token');
        });

        // Drop indexes for mass_sendings table
        Schema::table('mass_sendings', function (Blueprint $table) {
            $table->dropIndex('idx_mass_sendings_user_status');
            $table->dropIndex('idx_mass_sendings_status_created');
            $table->dropIndex('idx_mass_sendings_user_created');
            $table->dropIndex('idx_mass_sendings_connection');
        });

        // Drop indexes for whatsapp_connections table
        Schema::table('whatsapp_connections', function (Blueprint $table) {
            $table->dropIndex('idx_whatsapp_connections_user_status');
            $table->dropIndex('idx_whatsapp_connections_status_sync');
        });

        // Drop indexes for whatsapp_groups table
        Schema::table('whatsapp_groups', function (Blueprint $table) {
            $table->dropIndex('idx_whatsapp_groups_user_connection');
            $table->dropIndex('idx_whatsapp_groups_group_id');
        });

        // Drop indexes for extracted_contacts table
        Schema::table('extracted_contacts', function (Blueprint $table) {
            $table->dropIndex('idx_extracted_contacts_user_group');
            $table->dropIndex('idx_extracted_contacts_phone');
            $table->dropIndex('idx_extracted_contacts_user_created');
        });

        // Drop indexes for subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_subscriptions_user_status');
            $table->dropIndex('idx_subscriptions_status_expires');
            $table->dropIndex('idx_subscriptions_plan');
        });

        // Drop indexes for sent_messages table
        Schema::table('sent_messages', function (Blueprint $table) {
            $table->dropIndex('idx_sent_messages_campaign_status');
            $table->dropIndex('idx_sent_messages_campaign_type_id');
            $table->dropIndex('idx_sent_messages_status_sent_at');
        });

        // Drop indexes for groups table
        Schema::table('groups', function (Blueprint $table) {
            $table->dropIndex('idx_groups_user_created');
        });

        // Drop indexes for group_contacts table
        Schema::table('group_contacts', function (Blueprint $table) {
            $table->dropIndex('idx_group_contacts_group_contact');
        });
    }
};
