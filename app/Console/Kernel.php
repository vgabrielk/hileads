<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Test scheduler every minute
        $schedule->command('scheduler:test')
            ->everyMinute();
            
        // Process pending subscriptions every 2 minutes
        $schedule->command('subscriptions:smart-activate')
            ->everyTwoMinutes();
            
        // Process entitlements every 5 minutes for active subscriptions
        $schedule->command('stripe:force-entitlements')
            ->everyFiveMinutes();
            
        // Clean up old pending subscriptions (older than 24 hours)
        $schedule->command('subscriptions:cleanup')
            ->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
