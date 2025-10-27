<?php

/**
 * Auto-activation script for subscriptions
 * This script runs automatically via cron to activate pending subscriptions
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Subscription;
use App\Services\StripeService;

echo "🔄 Auto-activating subscriptions at " . now() . "\n";

try {
    $stripeService = app(StripeService::class);
    
    // Get recent pending subscriptions (last 24 hours)
    $pendingSubscriptions = Subscription::where('status', 'pending')
        ->whereNotNull('stripe_session_id')
        ->where('created_at', '>=', now()->subHours(24))
        ->get();
        
    if ($pendingSubscriptions->isEmpty()) {
        echo "ℹ️ No recent pending subscriptions to process\n";
        exit(0);
    }
    
    echo "Found {$pendingSubscriptions->count()} recent pending subscriptions\n";
    
    $processed = 0;
    $activated = 0;
    $errors = 0;
    
    foreach ($pendingSubscriptions as $subscription) {
        try {
            echo "Checking subscription ID: {$subscription->id}\n";
            
            // Get real status from Stripe
            $session = $stripeService->getCheckoutSession($subscription->stripe_session_id);
            
            echo "Session status: {$session->payment_status}\n";
            
            if ($session->payment_status === 'paid') {
                // Activate subscription
                $subscription->update([
                    'status' => 'active',
                    'stripe_customer_id' => $session->customer,
                    'stripe_subscription_id' => $session->subscription,
                    'starts_at' => now(),
                    'expires_at' => calculateExpirationDate($subscription->plan),
                    'metadata' => array_merge($subscription->metadata ?? [], [
                        'checkout_completed_at' => now()->toISOString(),
                        'session_id' => $session->id,
                        'payment_status' => $session->payment_status,
                        'auto_activated' => true
                    ])
                ]);
                
                echo "✅ Subscription {$subscription->id} activated\n";
                $activated++;
            } else {
                echo "⚠️ Subscription {$subscription->id} payment not completed (status: {$session->payment_status})\n";
            }
            
            $processed++;
            
        } catch (\Exception $e) {
            echo "❌ Error processing subscription {$subscription->id}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    
    echo "\n📊 Summary:\n";
    echo "• Processed: {$processed}\n";
    echo "• Activated: {$activated}\n";
    echo "• Errors: {$errors}\n";
    
} catch (\Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Calculate expiration date based on plan interval
 */
function calculateExpirationDate($plan): \DateTime
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
