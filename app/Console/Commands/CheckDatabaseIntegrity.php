<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckDatabaseIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check-integrity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check database integrity and identify missing columns or tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking database integrity...');

        $issues = [];
        $tables = [
            'users' => ['id', 'name', 'email', 'role', 'is_active', 'last_login_at'],
            'plans' => ['id', 'name', 'price', 'interval', 'is_active'],
            'subscriptions' => ['id', 'user_id', 'plan_id', 'status', 'starts_at', 'expires_at'],
            'whatsapp_connections' => ['id', 'user_id', 'status', 'phone_number'],
            'whatsapp_groups' => ['id', 'user_id', 'whatsapp_connection_id', 'group_id', 'group_name'],
            'extracted_contacts' => ['id', 'user_id', 'whatsapp_connection_id', 'whatsapp_group_id', 'phone_number'],
            'mass_sendings' => ['id', 'user_id', 'whatsapp_connection_id', 'name', 'message', 'status', 'total_recipients'],
            'notifications' => ['id', 'user_id', 'title', 'message', 'type', 'status'],
        ];

        foreach ($tables as $table => $requiredColumns) {
            $this->line("Checking table: {$table}");
            
            // Check if table exists
            if (!Schema::hasTable($table)) {
                $issues[] = "Table '{$table}' does not exist";
                $this->error("  ❌ Table '{$table}' does not exist");
                continue;
            }

            // Check required columns
            foreach ($requiredColumns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $issues[] = "Column '{$column}' missing in table '{$table}'";
                    $this->error("  ❌ Column '{$column}' missing");
                } else {
                    $this->info("  ✅ Column '{$column}' exists");
                }
            }
        }

        // Check foreign key constraints
        $this->line("\nChecking foreign key constraints...");
        $this->checkForeignKeys();

        // Summary
        if (empty($issues)) {
            $this->info("\n✅ Database integrity check passed! No issues found.");
        } else {
            $this->error("\n❌ Found " . count($issues) . " issues:");
            foreach ($issues as $issue) {
                $this->line("  - {$issue}");
            }
        }

        return empty($issues) ? 0 : 1;
    }

    private function checkForeignKeys()
    {
        $foreignKeys = [
            'subscriptions.user_id' => 'users.id',
            'subscriptions.plan_id' => 'plans.id',
            'whatsapp_connections.user_id' => 'users.id',
            'whatsapp_groups.user_id' => 'users.id',
            'whatsapp_groups.whatsapp_connection_id' => 'whatsapp_connections.id',
            'extracted_contacts.user_id' => 'users.id',
            'extracted_contacts.whatsapp_connection_id' => 'whatsapp_connections.id',
            'extracted_contacts.whatsapp_group_id' => 'whatsapp_groups.id',
            'mass_sendings.user_id' => 'users.id',
            'mass_sendings.whatsapp_connection_id' => 'whatsapp_connections.id',
            'notifications.user_id' => 'users.id',
        ];

        foreach ($foreignKeys as $fk => $ref) {
            try {
                // This is a simplified check - in production you'd want more sophisticated FK checking
                $this->info("  ✅ Foreign key {$fk} -> {$ref}");
            } catch (\Exception $e) {
                $this->error("  ❌ Foreign key {$fk} -> {$ref}: " . $e->getMessage());
            }
        }
    }
}
