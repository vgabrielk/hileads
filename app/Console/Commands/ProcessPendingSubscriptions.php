<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\StripeService;
use Stripe\Checkout\Session;

class ProcessPendingSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:process-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending subscriptions and activate them if payment was successful';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing pending subscriptions...');
        
        $pendingSubscriptions = Subscription::where('status', 'pending')
            ->whereNotNull('stripe_session_id')
            ->get();
            
        $this->info("Found {$pendingSubscriptions->count()} pending subscriptions");
        
        $stripeService = app(StripeService::class);
        $processed = 0;
        $activated = 0;
        
        foreach ($pendingSubscriptions as $subscription) {
            try {
                $this->info("Processing subscription ID: {$subscription->id}");
                
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
                            'processed_by' => 'manual_command'
                        ])
                    ]);
                    
                    $this->info("✅ Subscription {$subscription->id} activated");
                    $activated++;
                } else {
                    $this->warn("⚠️ Subscription {$subscription->id} payment not completed (status: {$session->payment_status})");
                }
                
                $processed++;
                
            } catch (\Exception $e) {
                $this->error("❌ Error processing subscription {$subscription->id}: " . $e->getMessage());
            }
        }
        
        $this->info('');
        $this->info("📊 Summary:");
        $this->info("• Processed: {$processed}");
        $this->info("• Activated: {$activated}");
        $this->info("• Remaining pending: " . Subscription::where('status', 'pending')->count());
        
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
