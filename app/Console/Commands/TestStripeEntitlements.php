<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use App\Services\StripeEntitlementsService;

class TestStripeEntitlements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:test-entitlements {--user=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Stripe entitlements for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Stripe Entitlements...');
        
        $userId = $this->option('user');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }
        
        $this->info("Testing entitlements for user: {$user->name} ({$user->email})");
        
        // Check user's active subscriptions
        $activeSubscriptions = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();
            
        $this->info("Active subscriptions: {$activeSubscriptions->count()}");
        
        if ($activeSubscriptions->isEmpty()) {
            $this->warn('No active subscriptions found for this user');
            return 0;
        }
        
        $entitlementsService = app(StripeEntitlementsService::class);
        
        foreach ($activeSubscriptions as $subscription) {
            $this->info("Checking subscription ID: {$subscription->id}");
            $this->info("Plan: {$subscription->plan->name}");
            $this->info("Stripe Customer ID: {$subscription->stripe_customer_id}");
            
            if (!$subscription->stripe_customer_id) {
                $this->warn('No Stripe customer ID found for this subscription');
                continue;
            }
            
            try {
                // Get customer entitlements
                $entitlements = $entitlementsService->getCustomerEntitlements($subscription->stripe_customer_id);
                
                $this->info("Found " . count($entitlements) . " entitlements:");
                
                foreach ($entitlements as $entitlement) {
                    $this->line("  • {$entitlement->feature->name} ({$entitlement->feature->lookup_key})");
                }
                
                // Test specific features
                $features = [
                    'api_access' => 'API Access',
                    'dashboard_access' => 'Dashboard Access',
                    'premium_support' => 'Premium Support',
                    'advanced_analytics' => 'Advanced Analytics',
                ];
                
                $this->info('');
                $this->info('Feature access check:');
                
                foreach ($features as $lookupKey => $name) {
                    $hasFeature = $entitlementsService->hasFeature($subscription->stripe_customer_id, $lookupKey);
                    $status = $hasFeature ? '✅' : '❌';
                    $this->line("  {$status} {$name}: " . ($hasFeature ? 'Granted' : 'Not granted'));
                }
                
            } catch (\Exception $e) {
                $this->error("Failed to get entitlements: " . $e->getMessage());
            }
        }
        
        $this->info('');
        $this->info('🎉 Entitlements test completed!');
        
        return 0;
    }
}
