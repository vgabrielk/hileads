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
        Schema::table('mass_sendings', function (Blueprint $table) {
            // Add missing columns only if they don't exist
            if (!Schema::hasColumn('mass_sendings', 'message_type')) {
                $table->string('message_type')->default('text')->after('message');
            }
            if (!Schema::hasColumn('mass_sendings', 'media_data')) {
                $table->json('media_data')->nullable()->after('message_type');
            }
            if (!Schema::hasColumn('mass_sendings', 'wuzapi_participants')) {
                $table->json('wuzapi_participants')->nullable()->after('contact_ids');
            }
            if (!Schema::hasColumn('mass_sendings', 'total_recipients')) {
                $table->integer('total_recipients')->default(0)->after('total_contacts');
            }
            if (!Schema::hasColumn('mass_sendings', 'failed_count')) {
                $table->integer('failed_count')->default(0)->after('replied_count');
            }
            if (!Schema::hasColumn('mass_sendings', 'notes')) {
                $table->text('notes')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('mass_sendings', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('mass_sendings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('failed_at');
            }
            if (!Schema::hasColumn('mass_sendings', 'whatsapp_connection_id')) {
                $table->foreignId('whatsapp_connection_id')->nullable()->constrained()->onDelete('set null')->after('user_id');
            }
            
            // Add indexes for better performance (only if they don't exist)
            $indexes = [
                ['status', 'created_at'],
                ['user_id', 'created_at'],
                ['message_type']
            ];
            
            foreach ($indexes as $index) {
                $indexName = 'mass_sendings_' . implode('_', $index) . '_index';
                if (!$this->indexExists('mass_sendings', $indexName)) {
                    $table->index($index);
                }
            }
        });
    }
    
    private function indexExists($table, $indexName)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mass_sendings', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['message_type']);
            
            // Drop foreign key and column
            $table->dropForeign(['whatsapp_connection_id']);
            $table->dropColumn('whatsapp_connection_id');
            
            // Drop added columns
            $table->dropColumn([
                'message_type',
                'media_data',
                'wuzapi_participants',
                'total_recipients',
                'failed_count',
                'notes',
                'failed_at',
                'cancelled_at'
            ]);
        });
    }
};
