<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;

class CleanupOldSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:cleanup {--days=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old pending subscriptions that were never paid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        
        $this->info("Cleaning up subscriptions older than {$days} day(s)...");
        
        // Find old pending subscriptions
        $oldSubscriptions = Subscription::where('status', 'pending')
            ->where('created_at', '<', now()->subDays($days))
            ->get();
            
        if ($oldSubscriptions->isEmpty()) {
            $this->info('No old subscriptions to clean up');
            return 0;
        }
        
        $this->info("Found {$oldSubscriptions->count()} old subscriptions to clean up");
        
        $deleted = 0;
        
        foreach ($oldSubscriptions as $subscription) {
            try {
                // Only delete if no payment was made
                if (!$subscription->stripe_customer_id && !$subscription->stripe_subscription_id) {
                    $subscription->delete();
                    $deleted++;
                    $this->info("Deleted subscription ID: {$subscription->id}");
                } else {
                    $this->warn("Keeping subscription ID: {$subscription->id} (has Stripe data)");
                }
            } catch (\Exception $e) {
                $this->error("Error deleting subscription {$subscription->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Cleanup completed. Deleted {$deleted} old subscriptions");
        
        return 0;
    }
}
