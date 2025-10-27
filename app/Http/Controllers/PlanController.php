<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display a listing of the plans.
     */
    public function index()
    {
        $plans = Plan::active()
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('plans.index', compact('plans'));
    }

    /**
     * Display the specified plan.
     */
    public function show(Plan $plan)
    {
        return view('plans.show', compact('plan'));
    }

    /**
     * Check if user has active subscription for specific plan.
     */
    private function checkUserSubscriptionForPlan($user, $plan)
    {
        if ($user->hasActiveSubscriptionForPlan($plan->id)) {
            $existingSubscription = $user->getLatestSubscriptionForPlan($plan->id);
            return redirect()->route('subscriptions.show', $existingSubscription)
                ->with('error', 'Você já possui uma assinatura ativa para este plano.');
        }
        return null;
    }

    /**
     * Show checkout page with iframe.
     */
    public function checkoutPage(Plan $plan)
    {
        $user = auth()->user();
        
        // Se o usuário é admin, não precisa de assinatura
        if ($user->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('info', 'Usuários administradores não precisam de assinatura.');
        }
        
        // Check if user already has an active subscription
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return redirect()->route('subscriptions.show', $activeSubscription)
                ->with('error', 'Você já possui uma assinatura ativa.');
        }

        try {
            Log::info('Creating Stripe checkout session for user', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'user_is_admin' => $user->isAdmin(),
                'plan_price' => $plan->price,
                'plan_price_cents' => $plan->price_cents
            ]);
            
            $checkoutData = $this->stripeService->createCheckoutSession($plan, $user);
            
            Log::info('Stripe checkout session created successfully', [
                'session_id' => $checkoutData['id'] ?? null,
                'url' => $checkoutData['url'] ?? null
            ]);

            // Create subscription record
            $subscription = $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'status' => 'pending',
                'stripe_session_id' => $checkoutData['id'] ?? null,
                'stripe_customer_id' => $checkoutData['stripe_customer_id'] ?? null,
                'starts_at' => now(),
                'expires_at' => $this->calculateExpirationDate($plan),
                'metadata' => [
                    'checkout_data' => $checkoutData,
                    'created_at' => now()->toISOString()
                ]
            ]);

            // Verificar se a URL do checkout foi retornada corretamente
            if (empty($checkoutData['url'])) {
                throw new \Exception('URL do checkout não foi retornada pela API do Stripe');
            }

            // Se for uma requisição AJAX, retornar JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $checkoutData['url'],
                    'message' => 'Redirecionando para o pagamento...'
                ]);
            }

            // Redirecionar diretamente para a URL do Stripe
            return redirect($checkoutData['url'])
                ->with('success', 'Redirecionando para o pagamento...');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar checkout: ' . $e->getMessage());
        }
    }

    /**
     * Create checkout for a plan.
     */
    public function checkout(Request $request, Plan $plan)
    {
        $user = auth()->user();
        
        // Se o usuário é admin, não precisa de assinatura
        if ($user->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('info', 'Usuários administradores não precisam de assinatura.');
        }
        
        // Check if user already has an active subscription
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return redirect()->route('subscriptions.show', $activeSubscription)
                ->with('error', 'Você já possui uma assinatura ativa.');
        }

        try {
            Log::info('Creating Stripe checkout session for user', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'user_is_admin' => $user->isAdmin(),
                'plan_price' => $plan->price,
                'plan_price_cents' => $plan->price_cents
            ]);
            
            $checkoutData = $this->stripeService->createCheckoutSession($plan, $user);
            
            Log::info('Stripe checkout session created successfully', [
                'session_id' => $checkoutData['id'] ?? null,
                'url' => $checkoutData['url'] ?? null
            ]);

            // Create subscription record
            $subscription = $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'status' => 'pending',
                'stripe_session_id' => $checkoutData['id'] ?? null,
                'stripe_customer_id' => $checkoutData['stripe_customer_id'] ?? null,
                'starts_at' => now(),
                'expires_at' => $this->calculateExpirationDate($plan),
                'metadata' => [
                    'checkout_data' => $checkoutData,
                    'created_at' => now()->toISOString()
                ]
            ]);

            // Verificar se a URL do checkout foi retornada corretamente
            if (empty($checkoutData['url'])) {
                throw new \Exception('URL do checkout não foi retornada pela API do Stripe');
            }

            // Se for uma requisição AJAX, retornar JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $checkoutData['url'],
                    'message' => 'Redirecionando para o pagamento...'
                ]);
            }

            // Redirecionar diretamente para a URL do Stripe
            return redirect($checkoutData['url'])
                ->with('success', 'Redirecionando para o pagamento...');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar checkout: ' . $e->getMessage());
        }
    }



    /**
     * Calculate expiration date based on plan interval.
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
}
