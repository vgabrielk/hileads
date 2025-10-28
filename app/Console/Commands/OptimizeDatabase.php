<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OptimizeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize MySQL database settings for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Optimizing MySQL database settings...');

        try {
            // MySQL optimization settings
            $optimizations = [
                'sort_buffer_size' => '16777216', // 16MB
                'read_buffer_size' => '8388608',  // 8MB
                'read_rnd_buffer_size' => '16777216', // 16MB
                'join_buffer_size' => '8388608',  // 8MB
                'tmp_table_size' => '67108864',   // 64MB
                'max_heap_table_size' => '67108864', // 64MB
            ];

            foreach ($optimizations as $variable => $value) {
                try {
                    DB::statement("SET GLOBAL {$variable} = {$value}");
                    $this->line("✓ Set {$variable} = {$value}");
                } catch (\Exception $e) {
                    $this->warn("⚠ Could not set {$variable}: " . $e->getMessage());
                }
            }

            // Show current settings
            $this->info("\nCurrent MySQL settings:");
            foreach (array_keys($optimizations) as $variable) {
                try {
                    $result = DB::select("SHOW VARIABLES LIKE '{$variable}'");
                    if (!empty($result)) {
                        $this->line("  {$variable}: " . $result[0]->Value);
                    }
                } catch (\Exception $e) {
                    $this->warn("  Could not read {$variable}");
                }
            }

            $this->info("\n✅ Database optimization completed!");
            $this->comment('Note: Some settings may require MySQL restart to take full effect.');

        } catch (\Exception $e) {
            $this->error("Failed to optimize database: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
