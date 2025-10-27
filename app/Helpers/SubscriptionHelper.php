<?php

namespace App\Helpers;

use App\Models\Subscription;
use App\Services\StripeEntitlementsService;
use Illuminate\Support\Facades\Auth;

class SubscriptionHelper
{
    /**
     * Check if user has access to a specific feature
     */
    public static function hasFeature(string $feature): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Check if user has active subscription
        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$activeSubscription) {
            return false;
        }

        // If no Stripe customer ID, grant basic access based on plan
        if (!$activeSubscription->stripe_customer_id) {
            return self::hasPlanFeature($activeSubscription->plan, $feature);
        }

        try {
            $entitlementsService = app(StripeEntitlementsService::class);
            
            // Check Stripe entitlements first
            if ($entitlementsService->hasFeature($activeSubscription->stripe_customer_id, $feature)) {
                return true;
            }
        } catch (\Exception $e) {
            // If Stripe entitlements fail, fall back to plan-based features
            \Log::warning('Stripe entitlements check failed, falling back to plan features', [
                'user_id' => $activeSubscription->user_id,
                'feature' => $feature,
                'error' => $e->getMessage()
            ]);
        }

        // Fallback to plan-based feature check
        return self::hasPlanFeature($activeSubscription->plan, $feature);
    }

    /**
     * Check if plan includes the feature
     */
    private static function hasPlanFeature($plan, string $feature): bool
    {
        $planFeatures = self::getPlanFeatures($plan);
        return in_array($feature, $planFeatures);
    }

    /**
     * Get features available for a plan
     */
    private static function getPlanFeatures($plan): array
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

    /**
     * Get user's active subscription
     */
    public static function getActiveSubscription()
    {
        if (!Auth::check()) {
            return null;
        }

        return Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with('plan')
            ->first();
    }

    /**
     * Check if user has any active subscription
     */
    public static function hasActiveSubscription(): bool
    {
        return self::getActiveSubscription() !== null;
    }

    /**
     * Get user's plan features
     */
    public static function getUserFeatures(): array
    {
        $subscription = self::getActiveSubscription();
        
        if (!$subscription) {
            return [];
        }

        return self::getPlanFeatures($subscription->plan);
    }

    /**
     * Check multiple features at once
     */
    public static function hasAnyFeature(array $features): bool
    {
        foreach ($features as $feature) {
            if (self::hasFeature($feature)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user has all features
     */
    public static function hasAllFeatures(array $features): bool
    {
        foreach ($features as $feature) {
            if (!self::hasFeature($feature)) {
                return false;
            }
        }
        
        return true;
    }
}
