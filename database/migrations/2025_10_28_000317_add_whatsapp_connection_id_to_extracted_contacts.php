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
        Schema::table('extracted_contacts', function (Blueprint $table) {
            // Add whatsapp_connection_id column if it doesn't exist
            if (!Schema::hasColumn('extracted_contacts', 'whatsapp_connection_id')) {
                $table->foreignId('whatsapp_connection_id')->nullable()->constrained()->onDelete('set null')->after('user_id');
            }
            
            // Add index for better performance
            $indexName = 'extracted_contacts_whatsapp_connection_id_index';
            if (!$this->indexExists('extracted_contacts', $indexName)) {
                $table->index('whatsapp_connection_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extracted_contacts', function (Blueprint $table) {
            // Drop index first
            $indexName = 'extracted_contacts_whatsapp_connection_id_index';
            if ($this->indexExists('extracted_contacts', $indexName)) {
                $table->dropIndex(['whatsapp_connection_id']);
            }
            
            // Drop foreign key and column
            if (Schema::hasColumn('extracted_contacts', 'whatsapp_connection_id')) {
                $table->dropForeign(['whatsapp_connection_id']);
                $table->dropColumn('whatsapp_connection_id');
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
};
