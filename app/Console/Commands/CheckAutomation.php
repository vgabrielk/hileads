<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;

class CheckAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automation:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if automation is working properly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Automation Status Check');
        $this->info('=========================');
        
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
        
        // Check if automation is running
        $this->info('');
        $this->info('🔄 Automation Status:');
        
        if ($this->isAutomationRunning()) {
            $this->info('✅ Automation is running');
            $this->info('• Auto-activation: Every 2 minutes');
            $this->info('• Log file: storage/logs/auto_activation.log');
        } else {
            $this->warn('⚠️ Automation may not be running');
            $this->warn('Check cron: crontab -l');
            $this->warn('Check logs: tail -f storage/logs/auto_activation.log');
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
            $this->info('The automation will process these automatically');
        }
        
        $this->info('');
        $this->info('🎯 Automation Commands:');
        $this->info('• Test manually: php auto_activate_subscriptions.php');
        $this->info('• Check logs: tail -f storage/logs/auto_activation.log');
        $this->info('• Check cron: crontab -l');
        
        return 0;
    }
    
    /**
     * Check if automation is running
     */
    private function isAutomationRunning(): bool
    {
        // Check if the automation log file exists and has recent entries
        $logFile = storage_path('logs/auto_activation.log');
        
        if (!file_exists($logFile)) {
            return false;
        }
        
        // Check if log file was updated in the last 10 minutes
        $lastModified = filemtime($logFile);
        return (time() - $lastModified) < 600; // 10 minutes
    }
}
