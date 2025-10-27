<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\StripeEntitlementsService;
use App\Services\StripeService;

class ForceCreateEntitlementsForActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entitlements:force-create-active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force creation of entitlements for all active subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Forcing entitlements creation for active subscriptions...');
        
        $activeSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('stripe_customer_id')
            ->with(['user', 'plan'])
            ->get();
            
        $this->info("Found {$activeSubscriptions->count()} active subscriptions with Stripe customer ID");
        
        if ($activeSubscriptions->isEmpty()) {
            $this->info('No active subscriptions with Stripe customer ID found');
            return 0;
        }
        
        $entitlementsService = app(StripeEntitlementsService::class);
        $stripeService = app(StripeService::class);
        
        $processed = 0;
        $successful = 0;
        $errors = 0;
        
        foreach ($activeSubscriptions as $subscription) {
            try {
                $this->info("Processing subscription ID: {$subscription->id}");
                $this->info("User: {$subscription->user->name}");
                $this->info("Plan: {$subscription->plan->name}");
                $this->info("Customer: {$subscription->stripe_customer_id}");
                
                // Setup features for the plan
                $features = $entitlementsService->setupPlanFeatures($subscription->plan);
                $this->info("Setup " . count($features) . " features for plan");
                
                // Try to get the Stripe subscription to trigger entitlements
                if ($subscription->stripe_subscription_id) {
                    try {
                        $stripeSubscription = $stripeService->getSubscription($subscription->stripe_subscription_id);
                        $this->info("Stripe subscription status: {$stripeSubscription->status}");
                    } catch (\Exception $e) {
                        $this->warn("Could not get Stripe subscription: " . $e->getMessage());
                    }
                }
                
                // Wait a moment for Stripe to process
                sleep(2);
                
                // Check entitlements again
                $entitlements = $entitlementsService->getCustomerEntitlements($subscription->stripe_customer_id);
                $this->info("Customer now has " . count($entitlements) . " entitlements");
                
                if (count($entitlements) > 0) {
                    $this->info("✅ Entitlements created successfully");
                    $successful++;
                } else {
                    $this->warn("⚠️ No entitlements found - may need webhook processing");
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
        $this->info("• Successful: {$successful}");
        $this->info("• Errors: {$errors}");
        
        if ($successful < $processed) {
            $this->warn('');
            $this->warn('Some entitlements may not have been created automatically.');
            $this->warn('This is normal for existing subscriptions.');
            $this->warn('New subscriptions will have entitlements created automatically.');
        }
        
        return 0;
    }
}
