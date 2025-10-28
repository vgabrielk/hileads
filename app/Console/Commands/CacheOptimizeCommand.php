<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Services\CacheService;

class CacheOptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:optimize 
                            {--clear : Clear all caches before optimization}
                            {--warm-up : Warm up frequently accessed caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize application cache for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cache optimization...');

        if ($this->option('clear')) {
            $this->info('Clearing all caches...');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            $this->info('All caches cleared successfully.');
        }

        if ($this->option('warm-up')) {
            $this->info('Warming up caches...');
            CacheService::warmUpCache();
            $this->info('Cache warm-up completed.');
        }

        // Optimize cache configuration
        $this->info('Optimizing cache configuration...');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        
        $this->info('Cache optimization completed successfully!');
        
        // Show cache statistics
        $stats = CacheService::getCacheStats();
        $this->table(['Metric', 'Value'], [
            ['Driver', $stats['driver']],
            ['Memory Usage', $stats['redis_info']['used_memory'] ?? 'N/A'],
            ['Connected Clients', $stats['redis_info']['connected_clients'] ?? 'N/A'],
        ]);

        return Command::SUCCESS;
    }
}
