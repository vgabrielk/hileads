<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for users table
        if (!$this->indexExists('users', 'idx_users_active_login') && 
            Schema::hasColumn('users', 'is_active') && 
            Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['is_active', 'last_login_at'], 'idx_users_active_login');
            });
        }
        
        if (!$this->indexExists('users', 'idx_users_role_active') && 
            Schema::hasColumn('users', 'role') && 
            Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['role', 'is_active'], 'idx_users_role_active');
            });
        }
        
        if (!$this->indexExists('users', 'idx_users_api_token') && 
            Schema::hasColumn('users', 'api_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('api_token', 'idx_users_api_token');
            });
        }

        // Add indexes for mass_sendings table
        if (!$this->indexExists('mass_sendings', 'idx_mass_sendings_user_status')) {
            Schema::table('mass_sendings', function (Blueprint $table) {
                $table->index(['user_id', 'status'], 'idx_mass_sendings_user_status');
            });
        }
        
        if (!$this->indexExists('mass_sendings', 'idx_mass_sendings_status_created')) {
            Schema::table('mass_sendings', function (Blueprint $table) {
                $table->index(['status', 'created_at'], 'idx_mass_sendings_status_created');
            });
        }
        
        if (!$this->indexExists('mass_sendings', 'idx_mass_sendings_user_created')) {
            Schema::table('mass_sendings', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'idx_mass_sendings_user_created');
            });
        }
        
        if (!$this->indexExists('mass_sendings', 'idx_mass_sendings_connection')) {
            Schema::table('mass_sendings', function (Blueprint $table) {
                $table->index('whatsapp_connection_id', 'idx_mass_sendings_connection');
            });
        }

        // Add indexes for whatsapp_connections table
        if (!$this->indexExists('whatsapp_connections', 'idx_whatsapp_connections_user_status')) {
            Schema::table('whatsapp_connections', function (Blueprint $table) {
                $table->index(['user_id', 'status'], 'idx_whatsapp_connections_user_status');
            });
        }
        
        if (!$this->indexExists('whatsapp_connections', 'idx_whatsapp_connections_status_sync')) {
            Schema::table('whatsapp_connections', function (Blueprint $table) {
                $table->index(['status', 'last_sync'], 'idx_whatsapp_connections_status_sync');
            });
        }

        // Add indexes for whatsapp_groups table
        if (!$this->indexExists('whatsapp_groups', 'idx_whatsapp_groups_user_connection')) {
            Schema::table('whatsapp_groups', function (Blueprint $table) {
                $table->index(['user_id', 'whatsapp_connection_id'], 'idx_whatsapp_groups_user_connection');
            });
        }
        
        if (!$this->indexExists('whatsapp_groups', 'idx_whatsapp_groups_group_id')) {
            Schema::table('whatsapp_groups', function (Blueprint $table) {
                $table->index('group_id', 'idx_whatsapp_groups_group_id');
            });
        }

        // Add indexes for extracted_contacts table
        if (!$this->indexExists('extracted_contacts', 'idx_extracted_contacts_user_group')) {
            Schema::table('extracted_contacts', function (Blueprint $table) {
                $table->index(['user_id', 'whatsapp_group_id'], 'idx_extracted_contacts_user_group');
            });
        }
        
        if (!$this->indexExists('extracted_contacts', 'idx_extracted_contacts_phone')) {
            Schema::table('extracted_contacts', function (Blueprint $table) {
                $table->index('phone_number', 'idx_extracted_contacts_phone');
            });
        }
        
        if (!$this->indexExists('extracted_contacts', 'idx_extracted_contacts_user_created')) {
            Schema::table('extracted_contacts', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'idx_extracted_contacts_user_created');
            });
        }

        // Add indexes for subscriptions table
        if (!$this->indexExists('subscriptions', 'idx_subscriptions_user_status')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->index(['user_id', 'status'], 'idx_subscriptions_user_status');
            });
        }
        
        if (!$this->indexExists('subscriptions', 'idx_subscriptions_status_expires')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->index(['status', 'expires_at'], 'idx_subscriptions_status_expires');
            });
        }
        
        if (!$this->indexExists('subscriptions', 'idx_subscriptions_plan')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->index('plan_id', 'idx_subscriptions_plan');
            });
        }

        // Add indexes for sent_messages table
        if (!$this->indexExists('sent_messages', 'idx_sent_messages_campaign_status')) {
            Schema::table('sent_messages', function (Blueprint $table) {
                $table->index(['campaign_id', 'status'], 'idx_sent_messages_campaign_status');
            });
        }
        
        if (!$this->indexExists('sent_messages', 'idx_sent_messages_campaign_type_id')) {
            Schema::table('sent_messages', function (Blueprint $table) {
                $table->index(['campaign_type', 'campaign_id'], 'idx_sent_messages_campaign_type_id');
            });
        }
        
        if (!$this->indexExists('sent_messages', 'idx_sent_messages_status_sent_at')) {
            Schema::table('sent_messages', function (Blueprint $table) {
                $table->index(['status', 'sent_at'], 'idx_sent_messages_status_sent_at');
            });
        }

        // Add indexes for groups table
        if (!$this->indexExists('groups', 'idx_groups_user_created')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'idx_groups_user_created');
            });
        }

        // Add indexes for group_contacts table
        if (!$this->indexExists('group_contacts', 'idx_group_contacts_group_contact')) {
            Schema::table('group_contacts', function (Blueprint $table) {
                $table->index(['group_id', 'contact_jid'], 'idx_group_contacts_group_contact');
            });
        }
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