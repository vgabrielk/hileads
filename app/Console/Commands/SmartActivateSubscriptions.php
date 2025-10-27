<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\StripeService;

class SmartActivateSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:smart-activate {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Smart activation of subscriptions - checks real Stripe status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Smart activation of subscriptions...');
        
        $stripeService = app(StripeService::class);
        
        // Get recent pending subscriptions (last 24 hours)
        $pendingSubscriptions = Subscription::where('status', 'pending')
            ->whereNotNull('stripe_session_id')
            ->where('created_at', '>=', now()->subHours(24))
            ->with(['user', 'plan'])
            ->get();
            
        $this->info("Found {$pendingSubscriptions->count()} recent pending subscriptions");
        
        if ($pendingSubscriptions->isEmpty()) {
            $this->info('No recent pending subscriptions to process');
            return 0;
        }
        
        $processed = 0;
        $activated = 0;
        $errors = 0;
        
        foreach ($pendingSubscriptions as $subscription) {
            try {
                $this->info("Checking subscription ID: {$subscription->id}");
                $this->info("User: {$subscription->user->name}");
                $this->info("Plan: {$subscription->plan->name}");
                $this->info("Session: {$subscription->stripe_session_id}");
                
                // Get real status from Stripe
                $session = $stripeService->getCheckoutSession($subscription->stripe_session_id);
                
                $this->info("Stripe session status: {$session->payment_status}");
                $this->info("Customer: {$session->customer}");
                $this->info("Subscription: {$session->subscription}");
                
                if ($session->payment_status === 'paid') {
                    if (!$dryRun) {
                        // Activate subscription
                        $subscription->update([
                            'status' => 'active',
                            'stripe_customer_id' => $session->customer,
                            'stripe_subscription_id' => $session->subscription,
                            'starts_at' => now(),
                            'expires_at' => $this->calculateExpirationDate($subscription->plan),
                            'metadata' => array_merge($subscription->metadata ?? [], [
                                'checkout_completed_at' => now()->toISOString(),
                                'session_id' => $session->id,
                                'payment_status' => $session->payment_status,
                                'smart_activated' => true
                            ])
                        ]);
                        
                        $this->info("✅ Subscription {$subscription->id} activated");
                        $activated++;
                    } else {
                        $this->info("✅ Would activate subscription {$subscription->id}");
                    }
                } else {
                    $this->warn("⚠️ Subscription {$subscription->id} payment not completed (status: {$session->payment_status})");
                }
                
                $processed++;
                
            } catch (\Exception $e) {
                $this->error("❌ Error processing subscription {$subscription->id}: " . $e->getMessage());
                $errors++;
            }
            
            $this->info('---');
        }
        
        $this->info('');
        $this->info("📊 Summary:");
        $this->info("• Processed: {$processed}");
        $this->info("• Activated: {$activated}");
        $this->info("• Errors: {$errors}");
        
        if ($dryRun) {
            $this->info('');
            $this->info('To actually activate the subscriptions, run without --dry-run');
        }
        
        return 0;
    }
    
    /**
     * Calculate expiration date based on plan interval
     */
    private function calculateExpirationDate($plan): \DateTime
    {
        $now = now();
        
        if ($plan->interval === 'monthly') {
            return $now->addMonths($plan->interval_count);
        } elseif ($plan->interval === 'yearly') {
            return $now->addYears($plan->interval_count);
        }
        
        // Default to monthly if interval is not recognized
        return $now->addMonth();
    }
}
