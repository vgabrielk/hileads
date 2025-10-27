<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Plan;
use App\Services\StripeEntitlementsService;
use App\Services\StripeService;

class ForceCreateEntitlements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:force-entitlements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force creation of entitlements for existing active subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Forcing creation of entitlements for active subscriptions...');
        
        $activeSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('stripe_customer_id')
            ->get();
            
        $this->info("Found {$activeSubscriptions->count()} active subscriptions");
        
        $entitlementsService = app(StripeEntitlementsService::class);
        $stripeService = app(StripeService::class);
        
        $processed = 0;
        $successful = 0;
        
        foreach ($activeSubscriptions as $subscription) {
            try {
                $this->info("Processing subscription ID: {$subscription->id}");
                $this->info("Plan: {$subscription->plan->name}");
                $this->info("Customer: {$subscription->stripe_customer_id}");
                
                // Get the Stripe subscription to trigger entitlements
                if ($subscription->stripe_subscription_id) {
                    $stripeSubscription = $stripeService->getSubscription($subscription->stripe_subscription_id);
                    $this->info("Stripe subscription status: {$stripeSubscription->status}");
                }
                
                // Setup features for the plan if not already done
                $features = $entitlementsService->setupPlanFeatures($subscription->plan);
                $this->info("Setup " . count($features) . " features for plan");
                
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
            }
        }
        
        $this->info('');
        $this->info("📊 Summary:");
        $this->info("• Processed: {$processed}");
        $this->info("• Successful: {$successful}");
        
        if ($successful < $processed) {
            $this->warn('');
            $this->warn('Some entitlements may not have been created automatically.');
            $this->warn('This is normal for existing subscriptions.');
            $this->warn('New subscriptions will have entitlements created automatically.');
        }
        
        return 0;
    }
}
