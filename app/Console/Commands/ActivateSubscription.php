<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\User;
use App\Services\StripeService;

class ActivateSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:activate {subscription_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually activate a specific subscription';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptionId = $this->argument('subscription_id');
        
        $subscription = Subscription::find($subscriptionId);
        
        if (!$subscription) {
            $this->error("Subscription with ID {$subscriptionId} not found");
            return 1;
        }
        
        $this->info("Found subscription ID: {$subscription->id}");
        $this->info("User: {$subscription->user->name} ({$subscription->user->email})");
        $this->info("Plan: {$subscription->plan->name}");
        $this->info("Current status: {$subscription->status}");
        
        if ($subscription->status === 'active') {
            $this->warn("Subscription is already active");
            return 0;
        }
        
        if (!$subscription->stripe_session_id) {
            $this->error("No Stripe session ID found for this subscription");
            return 1;
        }
        
        try {
            $stripeService = app(StripeService::class);
            $session = $stripeService->getCheckoutSession($subscription->stripe_session_id);
            
            $this->info("Stripe session status: {$session->payment_status}");
            
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
                        'manually_activated' => true
                    ])
                ]);
                
                $this->info("✅ Subscription {$subscription->id} activated successfully");
                
                // Show user info
                $user = $subscription->user;
                $this->info("User {$user->name} now has access to plan: {$subscription->plan->name}");
                
            } else {
                $this->error("Payment not completed. Session status: {$session->payment_status}");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to activate subscription: " . $e->getMessage());
            return 1;
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
