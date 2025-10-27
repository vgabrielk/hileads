<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Plan;
use Stripe\Stripe;
use Stripe\Entitlements\Feature;
use Stripe\Entitlements\ActiveEntitlement;

class StripeEntitlementsService
{
    private string $secretKey;

    public function __construct()
    {
        $mode = config('services.stripe.mode', 'sandbox');
        
        if ($mode === 'sandbox') {
            $this->secretKey = config('services.stripe.sandbox_secret_key') ?? '';
        } else {
            $this->secretKey = config('services.stripe.secret_key') ?? '';
        }
        
        if ($this->secretKey) {
            Stripe::setApiKey($this->secretKey);
        }
    }

    /**
     * Create a feature in Stripe
     */
    public function createFeature(string $name, string $lookupKey, array $metadata = []): \Stripe\Entitlements\Feature
    {
        try {
            $feature = Feature::create([
                'name' => $name,
                'lookup_key' => $lookupKey,
                'metadata' => $metadata,
            ]);

            Log::info('Stripe feature created', [
                'feature_id' => $feature->id,
                'name' => $name,
                'lookup_key' => $lookupKey
            ]);

            return $feature;
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe feature', [
                'name' => $name,
                'lookup_key' => $lookupKey,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get or create a feature
     */
    public function getOrCreateFeature(string $name, string $lookupKey, array $metadata = []): \Stripe\Entitlements\Feature
    {
        try {
            // Try to find existing feature
            $features = Feature::all(['limit' => 100]);
            
            foreach ($features->data as $feature) {
                if ($feature->lookup_key === $lookupKey) {
                    Log::info('Found existing Stripe feature', [
                        'feature_id' => $feature->id,
                        'lookup_key' => $lookupKey
                    ]);
                    return $feature;
                }
            }

            // Create new feature if not found
            return $this->createFeature($name, $lookupKey, $metadata);
        } catch (\Exception $e) {
            Log::error('Failed to get or create Stripe feature', [
                'name' => $name,
                'lookup_key' => $lookupKey,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Attach feature to product
     */
    public function attachFeatureToProduct(string $productId, string $featureId): \Stripe\ProductFeature
    {
        try {
            $productFeature = \Stripe\ProductFeature::create([
                'product' => $productId,
                'entitlement_feature' => $featureId,
            ]);

            Log::info('Feature attached to product', [
                'product_id' => $productId,
                'feature_id' => $featureId,
                'product_feature_id' => $productFeature->id
            ]);

            return $productFeature;
        } catch (\Exception $e) {
            Log::error('Failed to attach feature to product', [
                'product_id' => $productId,
                'feature_id' => $featureId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get customer's active entitlements
     */
    public function getCustomerEntitlements(string $customerId): array
    {
        try {
            $entitlements = ActiveEntitlement::all([
                'customer' => $customerId,
                'limit' => 100
            ]);

            Log::info('Retrieved customer entitlements', [
                'customer_id' => $customerId,
                'count' => count($entitlements->data)
            ]);

            return $entitlements->data;
        } catch (\Exception $e) {
            Log::error('Failed to get customer entitlements', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if customer has specific feature
     */
    public function hasFeature(string $customerId, string $lookupKey): bool
    {
        try {
            $entitlements = $this->getCustomerEntitlements($customerId);
            
            foreach ($entitlements as $entitlement) {
                if ($entitlement->feature->lookup_key === $lookupKey) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to check customer feature', [
                'customer_id' => $customerId,
                'lookup_key' => $lookupKey,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get customer's features as array
     */
    public function getCustomerFeatures(string $customerId): array
    {
        try {
            $entitlements = $this->getCustomerEntitlements($customerId);
            $features = [];
            
            foreach ($entitlements as $entitlement) {
                $features[] = [
                    'id' => $entitlement->feature->id,
                    'name' => $entitlement->feature->name,
                    'lookup_key' => $entitlement->feature->lookup_key,
                    'created' => $entitlement->created,
                ];
            }
            
            return $features;
        } catch (\Exception $e) {
            Log::error('Failed to get customer features', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Setup default features for a plan
     */
    public function setupPlanFeatures(Plan $plan): array
    {
        $features = [];
        
        // Create basic features based on plan
        $basicFeatures = [
            'api_access' => 'API Access',
            'dashboard_access' => 'Dashboard Access',
            'basic_support' => 'Basic Support',
        ];

        // Add premium features for higher plans
        if ($plan->price_cents >= 5000) { // R$ 50.00 or more
            $basicFeatures['premium_support'] = 'Premium Support';
            $basicFeatures['advanced_analytics'] = 'Advanced Analytics';
        }

        if ($plan->price_cents >= 10000) { // R$ 100.00 or more
            $basicFeatures['priority_support'] = 'Priority Support';
            $basicFeatures['custom_integrations'] = 'Custom Integrations';
        }

        foreach ($basicFeatures as $lookupKey => $name) {
            try {
                $feature = $this->getOrCreateFeature($name, $lookupKey, [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name
                ]);
                
                // Attach to product if we have a Stripe product ID
                if ($plan->stripe_product_id) {
                    $this->attachFeatureToProduct($plan->stripe_product_id, $feature->id);
                }
                
                $features[] = $feature;
            } catch (\Exception $e) {
                Log::error('Failed to setup feature for plan', [
                    'plan_id' => $plan->id,
                    'feature' => $lookupKey,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $features;
    }
}
