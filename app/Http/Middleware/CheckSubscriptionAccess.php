<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Services\StripeEntitlementsService;
use Illuminate\Support\Facades\Auth;

class CheckSubscriptionAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $feature = null): mixed
    {
        // Skip if user is not authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Check if user has active subscription
        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$activeSubscription) {
            return $this->handleNoAccess($request, 'Você precisa de uma assinatura ativa para acessar este recurso.');
        }

        // If no specific feature is required, just check for active subscription
        if (!$feature) {
            return $next($request);
        }

        // Check specific feature access
        if (!$this->hasFeatureAccess($activeSubscription, $feature)) {
            return $this->handleNoAccess($request, "Você não tem acesso ao recurso: {$feature}");
        }

        return $next($request);
    }

    /**
     * Check if user has access to a specific feature
     */
    private function hasFeatureAccess(Subscription $subscription, string $feature): bool
    {
        // If no Stripe customer ID, grant basic access based on plan
        if (!$subscription->stripe_customer_id) {
            return $this->hasPlanFeature($subscription->plan, $feature);
        }

        try {
            $entitlementsService = app(StripeEntitlementsService::class);
            
            // Check Stripe entitlements first
            if ($entitlementsService->hasFeature($subscription->stripe_customer_id, $feature)) {
                return true;
            }
        } catch (\Exception $e) {
            // If Stripe entitlements fail, fall back to plan-based features
            \Log::warning('Stripe entitlements check failed, falling back to plan features', [
                'user_id' => $subscription->user_id,
                'feature' => $feature,
                'error' => $e->getMessage()
            ]);
        }

        // Fallback to plan-based feature check
        return $this->hasPlanFeature($subscription->plan, $feature);
    }

    /**
     * Check if plan includes the feature
     */
    private function hasPlanFeature($plan, string $feature): bool
    {
        $planFeatures = $this->getPlanFeatures($plan);
        return in_array($feature, $planFeatures);
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

    /**
     * Handle no access response
     */
    private function handleNoAccess(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $message,
                'subscription_required' => true
            ], 403);
        }

        return redirect()->route('plans.index')
            ->with('error', $message);
    }
}
