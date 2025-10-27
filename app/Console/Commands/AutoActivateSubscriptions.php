<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;

class AutoActivateSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:auto-activate {--interval=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically activate subscriptions that have been paid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = $this->option('interval');
        
        $this->info("Auto-activating subscriptions (checking every {$interval} seconds)...");
        $this->info("Press Ctrl+C to stop");
        
        $stripeService = app(StripeService::class);
        
        while (true) {
            try {
                $this->processPendingSubscriptions($stripeService);
                sleep($interval);
            } catch (\Exception $e) {
                $this->error("Error in auto-activation: " . $e->getMessage());
                sleep(5);
            }
        }
    }
    
    private function processPendingSubscriptions(StripeService $stripeService)
    {
        $pendingSubscriptions = Subscription::where('status', 'pending')
            ->whereNotNull('stripe_session_id')
            ->where('created_at', '>=', now()->subHours(24)) // Only check recent subscriptions
            ->get();
            
        if ($pendingSubscriptions->isEmpty()) {
            $this->info("No recent pending subscriptions to process");
            return;
        }
        
        $this->info("Found {$pendingSubscriptions->count()} recent pending subscriptions");
        
        foreach ($pendingSubscriptions as $subscription) {
            try {
                $this->info("Checking subscription ID: {$subscription->id}");
                
                // Get checkout session from Stripe
                $session = $stripeService->getCheckoutSession($subscription->stripe_session_id);
                
                $this->info("Session status: {$session->payment_status}");
                
                if ($session->payment_status === 'paid') {
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
                            'auto_activated' => true
                        ])
                    ]);
                    
                    $this->info("✅ Subscription {$subscription->id} activated automatically");
                    
                    Log::info('Subscription auto-activated', [
                        'subscription_id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                        'plan_id' => $subscription->plan_id,
                        'session_id' => $session->id
                    ]);
                } else {
                    $this->info("⏳ Subscription {$subscription->id} still pending (status: {$session->payment_status})");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error checking subscription {$subscription->id}: " . $e->getMessage());
            }
        }
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
