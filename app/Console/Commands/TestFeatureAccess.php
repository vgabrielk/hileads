<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use App\Helpers\SubscriptionHelper;

class TestFeatureAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'features:test {--user=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test feature access for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Feature Access');
        $this->info('========================');
        
        $userId = $this->option('user');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }
        
        $this->info("Testing feature access for user: {$user->name} ({$user->email})");
        
        // Check if user has active subscription directly
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('plan')
            ->first();
            
        $hasActiveSubscription = $subscription !== null;
        $this->info("Has active subscription: " . ($hasActiveSubscription ? '✅ Yes' : '❌ No'));
        
        if (!$hasActiveSubscription) {
            $this->warn('User does not have an active subscription');
            return 0;
        }
        
        $this->info("Active subscription: {$subscription->plan->name} (R$ {$subscription->plan->price})");
        
        // Get user's features based on plan
        $userFeatures = $this->getPlanFeatures($subscription->plan);
        $this->info("User features: " . implode(', ', $userFeatures));
        
        // Test specific features
        $features = [
            'api_access' => 'API Access',
            'dashboard_access' => 'Dashboard Access',
            'basic_support' => 'Basic Support',
            'premium_support' => 'Premium Support',
            'advanced_analytics' => 'Advanced Analytics',
            'priority_support' => 'Priority Support',
            'custom_integrations' => 'Custom Integrations',
        ];
        
        $this->info('');
        $this->info('🔍 Feature Access Test:');
        
        foreach ($features as $feature => $name) {
            $hasAccess = in_array($feature, $userFeatures);
            $status = $hasAccess ? '✅' : '❌';
            $this->line("  {$status} {$name}: " . ($hasAccess ? 'Granted' : 'Not granted'));
        }
        
        // Test middleware functionality
        $this->info('');
        $this->info('🛡️ Middleware Test:');
        
        // Simulate middleware checks
        $middlewareFeatures = ['api_access', 'dashboard_access', 'premium_support'];
        
        foreach ($middlewareFeatures as $feature) {
            $hasAccess = in_array($feature, $userFeatures);
            $status = $hasAccess ? '✅' : '❌';
            $this->line("  {$status} Middleware would allow: {$feature}");
        }
        
        $this->info('');
        $this->info('🎯 Summary:');
        $this->info("• User has active subscription: " . ($hasActiveSubscription ? 'Yes' : 'No'));
        $this->info("• Available features: " . count($userFeatures));
        $this->info("• Features: " . implode(', ', $userFeatures));
        
        return 0;
    }
    
    /**
     * Get features available for a plan
     */
    private function getPlanFeatures($plan): array
    {
        $features = ['api_access', 'dashboard_access', 'basic_support'];

        // Add premium features based on plan price
        if ($plan->price_cents >= 5000) { // R$ 50.00 or more
            $features[] = 'premium_support';
            $features[] = 'advanced_analytics';
        }

        if ($plan->price_cents >= 10000) { // R$ 100.00 or more
            $features[] = 'priority_support';
            $features[] = 'custom_integrations';
        }

        return $features;
    }
}
