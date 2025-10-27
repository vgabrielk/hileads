<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StripeService;
use App\Models\Plan;
use App\Models\User;

class TestStripeIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:test {--plan=1} {--user=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Stripe integration with a plan and user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Stripe Integration...');
        
        // Show current mode
        $mode = config('services.stripe.mode', 'sandbox');
        $this->info("Current mode: {$mode}");
        
        if ($mode === 'sandbox') {
            $this->info('🧪 Using SANDBOX mode - Safe for development (Recommended by Stripe)');
        } elseif ($mode === 'test') {
            $this->info('🧪 Using TEST mode - Safe for development (Legacy)');
        } else {
            $this->warn('⚠️  Using LIVE mode - Real payments will be processed!');
        }
        
        try {
            $stripeService = app(StripeService::class);
            
            // Test connection
            $this->info('1. Testing Stripe connection...');
            $connectionTest = $stripeService->testConnection();
            
            if ($connectionTest['success']) {
                $this->info('✅ Stripe connection successful');
            } else {
                $this->error('❌ Stripe connection failed: ' . $connectionTest['error']);
                return 1;
            }
            
            // Get plan and user
            $planId = $this->option('plan');
            $userId = $this->option('user');
            
            $plan = Plan::find($planId);
            $user = User::find($userId);
            
            if (!$plan) {
                $this->error("❌ Plan with ID {$planId} not found");
                return 1;
            }
            
            if (!$user) {
                $this->error("❌ User with ID {$userId} not found");
                return 1;
            }
            
            $this->info("Using Plan: {$plan->name} (R$ {$plan->price})");
            $this->info("Using User: {$user->name} ({$user->email})");
            
            // Test checkout session creation
            $this->info('2. Testing checkout session creation...');
            
            try {
                $checkoutData = $stripeService->createCheckoutSession($plan, $user);
                
                $this->info('✅ Checkout session created successfully');
                $this->info("Session ID: {$checkoutData['id']}");
                $this->info("Checkout URL: {$checkoutData['url']}");
                $this->info("Customer ID: {$checkoutData['stripe_customer_id']}");
                
                // Create subscription record
                $subscription = $user->subscriptions()->create([
                    'plan_id' => $plan->id,
                    'status' => 'pending',
                    'stripe_session_id' => $checkoutData['id'],
                    'stripe_customer_id' => $checkoutData['stripe_customer_id'],
                    'starts_at' => now(),
                    'expires_at' => now()->addMonth(),
                    'metadata' => [
                        'test_checkout' => true,
                        'created_at' => now()->toISOString()
                    ]
                ]);
                
                $this->info("✅ Subscription record created with ID: {$subscription->id}");
                
            } catch (\Exception $e) {
                $this->error('❌ Checkout session creation failed: ' . $e->getMessage());
                return 1;
            }
            
            $this->info('3. Testing public key retrieval...');
            $publicKey = $stripeService->getPublicKey();
            $this->info("✅ Public key: {$publicKey}");
            
            $this->info('');
            $this->info('🎉 Stripe integration test completed successfully!');
            $this->info('');
            
            if ($mode === 'sandbox' || $mode === 'test') {
                $this->info('🧪 Testing Environment Information:');
                $this->info('Use these test cards:');
                $this->info('• 4242424242424242 - Visa (success)');
                $this->info('• 4000056655665556 - Visa (debit)');
                $this->info('• 5555555555554444 - Mastercard');
                $this->info('• 4000000000000002 - Card declined');
                $this->info('• 4000000000009995 - Insufficient funds');
                $this->info('• Use any 3-digit CVC');
                $this->info('');
            }
            
            $this->info('Next steps:');
            $this->info('1. Configure webhook endpoint in Stripe Dashboard');
            $this->info('2. Set STRIPE_WEBHOOK_SECRET in your .env file');
            $this->info('3. Test the complete payment flow');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }
}