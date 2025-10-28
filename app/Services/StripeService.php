<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeService
{
    private string $secretKey;
    private string $publicKey;
    private string $webhookSecret;

    public function __construct()
    {
        $mode = config('services.stripe.mode', 'sandbox');
        
        if ($mode === 'sandbox') {
            $this->secretKey = config('services.stripe.sandbox_secret_key') ?? '';
            $this->publicKey = config('services.stripe.sandbox_public_key') ?? '';
            $this->webhookSecret = config('services.stripe.sandbox_webhook_secret') ?? '';
        } else {
            $this->secretKey = config('services.stripe.secret_key') ?? '';
            $this->publicKey = config('services.stripe.public_key') ?? '';
            $this->webhookSecret = config('services.stripe.webhook_secret') ?? '';
        }
        
        if ($this->secretKey) {
            Stripe::setApiKey($this->secretKey);
        }
    }

    /**
     * Create a checkout session for a plan
     */
    public function createCheckoutSession(Plan $plan, User $user, string $successUrl = null, string $cancelUrl = null): array
    {
        try {
            // Validar dados de entrada
            $this->validateCheckoutData($plan, $user);

            Log::info('Creating Stripe checkout session', [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'amount' => $plan->price_cents
            ]);

            // Criar ou obter produto no Stripe
            $stripeProduct = $this->getOrCreateStripeProduct($plan);
            
            // Criar ou obter preço no Stripe
            $stripePrice = $this->getOrCreateStripePrice($plan, $stripeProduct);

            // Criar ou obter customer no Stripe
            $stripeCustomer = $this->getOrCreateStripeCustomer($user);

            // URLs padrão
            $successUrl = $successUrl ?? route('subscriptions.success');
            $cancelUrl = $cancelUrl ?? route('plans.show', $plan);

            // Criar sessão de checkout
            $session = Session::create([
                'customer' => $stripeCustomer->id,
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $stripePrice->id,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
            ]);

            Log::info('Stripe checkout session created successfully', [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'session_id' => $session->id,
                'url' => $session->url
            ]);

            return [
                'id' => $session->id,
                'url' => $session->url,
                'stripe_product_id' => $stripeProduct->id,
                'stripe_price_id' => $stripePrice->id,
                'stripe_customer_id' => $stripeCustomer->id,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation error', [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get or create Stripe product
     */
    private function getOrCreateStripeProduct(Plan $plan): \Stripe\Product
    {
        // Verificar se já existe um produto com o metadata plan_id
        $products = Product::all(['limit' => 100]);
        
        foreach ($products->data as $product) {
            if (isset($product->metadata['plan_id']) && $product->metadata['plan_id'] == $plan->id) {
                return $product;
            }
        }

        // Criar novo produto
        return Product::create([
            'name' => $plan->name,
            'description' => $plan->description,
            'metadata' => [
                'plan_id' => $plan->id,
            ],
        ]);
    }

    /**
     * Get or create Stripe price
     */
    private function getOrCreateStripePrice(Plan $plan, \Stripe\Product $stripeProduct): \Stripe\Price
    {
        // Verificar se já existe um preço com o metadata plan_id
        $prices = Price::all([
            'product' => $stripeProduct->id,
            'limit' => 100
        ]);
        
        foreach ($prices->data as $price) {
            if (isset($price->metadata['plan_id']) && $price->metadata['plan_id'] == $plan->id) {
                return $price;
            }
        }

        // Criar novo preço
        return Price::create([
            'product' => $stripeProduct->id,
            'unit_amount' => $plan->price_cents,
            'currency' => 'brl',
            'recurring' => [
                'interval' => $plan->interval === 'monthly' ? 'month' : 'year',
                'interval_count' => $plan->interval_count ?? 1,
            ],
            'metadata' => [
                'plan_id' => $plan->id,
            ],
        ]);
    }

    /**
     * Get or create Stripe customer
     */
    private function getOrCreateStripeCustomer(User $user): \Stripe\Customer
    {
        // Verificar se já existe um customer com o metadata user_id
        $customers = Customer::all(['limit' => 100]);
        
        foreach ($customers->data as $customer) {
            if (isset($customer->metadata['user_id']) && $customer->metadata['user_id'] == $user->id) {
                return $customer;
            }
        }

        // Criar novo customer
        return Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);
    }

    /**
     * Get checkout session details
     */
    public function getCheckoutSession(string $sessionId): \Stripe\Checkout\Session
    {
        try {
            return Session::retrieve($sessionId);
        } catch (\Exception $e) {
            Log::error('Failed to get Stripe checkout session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get subscription details
     */
    public function getSubscription(string $subscriptionId): \Stripe\Subscription
    {
        try {
            return StripeSubscription::retrieve($subscriptionId);
        } catch (\Exception $e) {
            Log::error('Failed to get Stripe subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Process webhook from Stripe
     */
    public function processWebhook(string $payload, string $signature): void
    {
        try {
            // Verificar assinatura do webhook
            $event = Webhook::constructEvent($payload, $signature, $this->webhookSecret);

            Log::info('Processing Stripe webhook', [
                'event_type' => $event->type,
                'event_id' => $event->id
            ]);

            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;
                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;
                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'event_type' => $event->type,
                        'event_id' => $event->id
                    ]);
            }

        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Handle checkout session completed
     */
    private function handleCheckoutSessionCompleted($session): void
    {
        Log::info('Processing checkout session completed', [
            'session_id' => $session->id,
            'payment_status' => $session->payment_status,
            'customer' => $session->customer,
            'subscription' => $session->subscription
        ]);

        // Encontrar assinatura pela sessão
        $subscription = Subscription::where('stripe_session_id', $session->id)->first();

        if (!$subscription) {
            Log::error('Subscription not found for checkout session', [
                'session_id' => $session->id
            ]);
            return;
        }

        // Atualizar assinatura com dados da sessão
        $subscription->update([
            'status' => 'active',
            'stripe_customer_id' => $session->customer,
            'stripe_subscription_id' => $session->subscription,
            'starts_at' => now(),
            'expires_at' => $this->calculateExpirationDate($subscription->plan),
            'metadata' => array_merge($subscription->metadata ?? [], [
                'checkout_completed_at' => now()->toISOString(),
                'session_id' => $session->id,
                'payment_status' => $session->payment_status
            ])
        ]);

        Log::info('Subscription activated via checkout session', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'plan_id' => $subscription->plan_id,
            'session_id' => $session->id
        ]);
    }

    /**
     * Handle subscription updated
     */
    private function handleSubscriptionUpdated($stripeSubscription): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::error('Subscription not found for Stripe subscription', [
                'stripe_subscription_id' => $stripeSubscription->id
            ]);
            return;
        }

        $status = $this->mapStripeStatusToLocal($stripeSubscription->status);

        $subscription->update([
            'status' => $status,
            'metadata' => array_merge($subscription->metadata ?? [], [
                'stripe_status' => $stripeSubscription->status,
                'updated_at' => now()->toISOString()
            ])
        ]);

        Log::info('Subscription status updated', [
            'subscription_id' => $subscription->id,
            'status' => $status,
            'stripe_status' => $stripeSubscription->status
        ]);
    }

    /**
     * Handle subscription deleted
     */
    private function handleSubscriptionDeleted($stripeSubscription): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::error('Subscription not found for deleted Stripe subscription', [
                'stripe_subscription_id' => $stripeSubscription->id
            ]);
            return;
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'metadata' => array_merge($subscription->metadata ?? [], [
                'cancelled_via_stripe' => true,
                'cancelled_at' => now()->toISOString()
            ])
        ]);

        Log::info('Subscription cancelled via Stripe', [
            'subscription_id' => $subscription->id,
            'stripe_subscription_id' => $stripeSubscription->id
        ]);
    }

    /**
     * Handle payment succeeded
     */
    private function handlePaymentSucceeded($invoice): void
    {
        $stripeSubscriptionId = $invoice->subscription;
        
        if (!$stripeSubscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            return;
        }

        // Renovar assinatura
        $subscription->update([
            'status' => 'active',
            'expires_at' => $this->calculateExpirationDate($subscription->plan),
            'metadata' => array_merge($subscription->metadata ?? [], [
                'last_payment_at' => now()->toISOString(),
                'invoice_id' => $invoice->id
            ])
        ]);

        Log::info('Subscription renewed via payment', [
            'subscription_id' => $subscription->id,
            'invoice_id' => $invoice->id
        ]);
    }

    /**
     * Handle payment failed
     */
    private function handlePaymentFailed($invoice): void
    {
        $stripeSubscriptionId = $invoice->subscription;
        
        if (!$stripeSubscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => 'past_due',
            'metadata' => array_merge($subscription->metadata ?? [], [
                'payment_failed_at' => now()->toISOString(),
                'invoice_id' => $invoice->id
            ])
        ]);

        Log::info('Subscription payment failed', [
            'subscription_id' => $subscription->id,
            'invoice_id' => $invoice->id
        ]);
    }

    /**
     * Map Stripe status to local status
     */
    private function mapStripeStatusToLocal(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'active' => 'active',
            'past_due' => 'past_due',
            'canceled' => 'cancelled',
            'unpaid' => 'past_due',
            'incomplete' => 'pending',
            'incomplete_expired' => 'cancelled',
            'trialing' => 'active',
            default => 'pending'
        };
    }

    /**
     * Calculate expiration date based on plan interval
     */
    private function calculateExpirationDate(Plan $plan): \DateTime
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

    /**
     * Get public key for frontend
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        try {
            if (!$subscription->stripe_subscription_id) {
                throw new \Exception('No Stripe subscription ID found');
            }

            $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);
            $stripeSubscription->cancel();

            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'metadata' => array_merge($subscription->metadata ?? [], [
                    'cancelled_via_api' => true,
                    'cancelled_at' => now()->toISOString()
                ])
            ]);

            Log::info('Subscription cancelled via API', [
                'subscription_id' => $subscription->id,
                'stripe_subscription_id' => $subscription->stripe_subscription_id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to cancel Stripe subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Validate checkout data
     */
    private function validateCheckoutData(Plan $plan, User $user): void
    {
        if (!$plan || !$plan->price_cents || $plan->price_cents <= 0) {
            throw new \InvalidArgumentException('Plan must have a valid price in cents');
        }

        if (!$user || !$user->id) {
            throw new \InvalidArgumentException('User is required');
        }

        if (!$this->secretKey) {
            throw new \InvalidArgumentException('Stripe secret key is not configured');
        }
    }

    /**
     * Test connection to Stripe API
     */
    public function testConnection(): array
    {
        try {
            Log::info('Testing Stripe API connection', [
                'has_secret_key' => !empty($this->secretKey)
            ]);

            // Tenta listar produtos para testar a autenticação
            $products = Product::all(['limit' => 1]);

            Log::info('Stripe API connection test successful');

            return [
                'success' => true,
                'message' => 'Connection successful'
            ];
        } catch (\Exception $e) {
            Log::error('Stripe API connection test failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Activate subscription in Stripe
     */
    public function activateSubscription(string $subscriptionId): \Stripe\Subscription
    {
        try {
            $subscription = StripeSubscription::retrieve($subscriptionId);
            $subscription->status = 'active';
            return $subscription->save();
        } catch (\Exception $e) {
            Log::error('Failed to activate Stripe subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
