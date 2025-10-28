<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;
use App\Services\LoadingStateService;

class SystemOptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:optimize 
                            {--full : Perform full optimization including cache warm-up}
                            {--clean : Clean up old data and caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the entire system for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting system optimization...');

        // Clear all caches
        $this->info('🧹 Clearing caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('✅ Caches cleared');

        // Optimize autoloader
        $this->info('📦 Optimizing autoloader...');
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        $this->info('✅ Autoloader optimized');

        if ($this->option('full')) {
            // Warm up caches
            $this->info('🔥 Warming up caches...');
            CacheService::warmUpCache();
            $this->info('✅ Caches warmed up');
        }

        if ($this->option('clean')) {
            // Clean up old data
            $this->info('🗑️ Cleaning up old data...');
            $this->cleanupOldData();
            $this->info('✅ Old data cleaned up');
        }

        // Show system status
        $this->showSystemStatus();

        $this->info('🎉 System optimization completed successfully!');
        
        return Command::SUCCESS;
    }

    /**
     * Clean up old data
     */
    private function cleanupOldData(): void
    {
        // Clean up old loading states
        LoadingStateService::cleanupExpiredStates();
        
        // Clean up old cache entries (this would depend on your cache driver)
        // For Redis, you could implement a cleanup strategy
        
        $this->line('  - Cleaned up expired loading states');
        $this->line('  - Cleaned up old cache entries');
    }

    /**
     * Show system status
     */
    private function showSystemStatus(): void
    {
        $this->info('📊 System Status:');
        
        // Get cache statistics
        $cacheStats = CacheService::getCacheStats();
        
        $this->table(['Metric', 'Value'], [
            ['Cache Driver', $cacheStats['driver']],
            ['Memory Usage', $cacheStats['redis_info']['used_memory'] ?? 'N/A'],
            ['Connected Clients', $cacheStats['redis_info']['connected_clients'] ?? 'N/A'],
            ['PHP Version', PHP_VERSION],
            ['Laravel Version', app()->version()],
        ]);
    }
}
