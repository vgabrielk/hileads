<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;

class CheckScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the scheduler is working and show subscription stats';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Laravel Scheduler Status Check');
        $this->info('================================');
        
        // Check subscription stats
        $total = Subscription::count();
        $active = Subscription::where('status', 'active')->count();
        $pending = Subscription::where('status', 'pending')->count();
        $recentPending = Subscription::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
            
        $this->info("📊 Subscription Statistics:");
        $this->info("• Total subscriptions: {$total}");
        $this->info("• Active subscriptions: {$active}");
        $this->info("• Pending subscriptions: {$pending}");
        $this->info("• Recent pending (24h): {$recentPending}");
        
        // Check if scheduler is running
        $this->info('');
        $this->info('🕐 Scheduler Status:');
        
        if ($this->isSchedulerRunning()) {
            $this->info('✅ Scheduler is running');
            $this->info('• Auto-activation: Every 2 minutes');
            $this->info('• Entitlements: Every 5 minutes');
            $this->info('• Cleanup: Every hour');
        } else {
            $this->warn('⚠️ Scheduler may not be running');
            $this->warn('Make sure to add this to your crontab:');
            $this->warn('* * * * * cd ' . base_path() . ' && php artisan schedule:run >> /dev/null 2>&1');
        }
        
        // Show recent activity
        $this->info('');
        $this->info('📈 Recent Activity:');
        
        $recentActive = Subscription::where('status', 'active')
            ->where('updated_at', '>=', now()->subHours(24))
            ->count();
            
        if ($recentActive > 0) {
            $this->info("✅ {$recentActive} subscriptions activated in the last 24 hours");
        } else {
            $this->info('ℹ️ No recent activations');
        }
        
        // Show pending subscriptions that need attention
        if ($recentPending > 0) {
            $this->info('');
            $this->warn("⚠️ {$recentPending} recent pending subscriptions need processing");
            $this->info('The scheduler will process these automatically');
        }
        
        $this->info('');
        $this->info('🎯 Next Steps:');
        $this->info('1. Make sure cron is running: crontab -l');
        $this->info('2. Check logs: tail -f storage/logs/scheduler.log');
        $this->info('3. Test manually: php artisan subscriptions:smart-activate');
        
        return 0;
    }
    
    /**
     * Check if the scheduler is running
     */
    private function isSchedulerRunning(): bool
    {
        // Check if the scheduler log file exists and has recent entries
        $logFile = storage_path('logs/scheduler.log');
        
        if (!file_exists($logFile)) {
            return false;
        }
        
        // Check if log file was updated in the last 10 minutes
        $lastModified = filemtime($logFile);
        return (time() - $lastModified) < 600; // 10 minutes
    }
}
