<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class OptimizeMemory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memory:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize memory usage and run garbage collection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Optimizing memory usage...');
        
        // Get current memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        
        $this->info("📊 Current memory usage: " . $this->formatBytes($memoryUsage));
        $this->info("📈 Peak memory usage: " . $this->formatBytes($memoryPeak));
        $this->info("🔒 Memory limit: " . $this->formatBytes($memoryLimit));
        
        // Calculate usage percentage
        $usagePercentage = ($memoryUsage / $memoryLimit) * 100;
        $this->info("📊 Memory usage: " . number_format($usagePercentage, 2) . "%");
        
        // Run garbage collection
        if (function_exists('gc_collect_cycles')) {
            $collected = gc_collect_cycles();
            $this->info("🗑️  Garbage collection: {$collected} cycles collected");
        }
        
        // Force garbage collection
        if (function_exists('gc_mem_caches')) {
            gc_mem_caches();
            $this->info("🧹 Memory caches cleared");
        }
        
        // Get memory usage after optimization
        $memoryUsageAfter = memory_get_usage(true);
        $saved = $memoryUsage - $memoryUsageAfter;
        
        $this->info("💾 Memory saved: " . $this->formatBytes($saved));
        $this->info("✅ Memory optimization completed!");
        
        // Log the optimization
        Log::info('Memory optimization completed', [
            'memory_before' => $memoryUsage,
            'memory_after' => $memoryUsageAfter,
            'memory_saved' => $saved,
            'usage_percentage' => $usagePercentage
        ]);
        
        return 0;
    }
    
    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit($memoryLimit)
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $value = (int) $memoryLimit;
        
        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
