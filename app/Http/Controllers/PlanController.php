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
     * Display admin management of plans.
     */
    public function admin()
    {
        $this->authorize('manage', Plan::class);
        
        $plans = Plan::orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('plans.admin', compact('plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        $this->authorize('create', Plan::class);
        
        return view('plans.create');
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Plan::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|in:monthly,yearly',
            'interval_count' => 'required|integer|min:1',
            'max_contacts' => 'nullable|integer|min:0',
            'max_campaigns' => 'nullable|integer|min:0',
            'max_mass_sendings' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $plan = Plan::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'price_cents' => (int) ($request->price * 100),
            'interval' => $request->interval,
            'interval_count' => $request->interval_count,
            'max_contacts' => $request->max_contacts,
            'max_campaigns' => $request->max_campaigns,
            'max_mass_sendings' => $request->max_mass_sendings,
            'features' => $request->features ?? [],
            'is_active' => $request->boolean('is_active', true),
            'is_popular' => $request->boolean('is_popular', false),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('plans.admin')
            ->with('success', 'Plano criado com sucesso!');
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan)
    {
        $this->authorize('update', $plan);
        
        return view('plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Plan $plan)
    {
        $this->authorize('update', $plan);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|in:monthly,yearly',
            'interval_count' => 'required|integer|min:1',
            'max_contacts' => 'nullable|integer|min:0',
            'max_campaigns' => 'nullable|integer|min:0',
            'max_mass_sendings' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $plan->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'price_cents' => (int) ($request->price * 100),
            'interval' => $request->interval,
            'interval_count' => $request->interval_count,
            'max_contacts' => $request->max_contacts,
            'max_campaigns' => $request->max_campaigns,
            'max_mass_sendings' => $request->max_mass_sendings,
            'features' => $request->features ?? [],
            'is_active' => $request->boolean('is_active', true),
            'is_popular' => $request->boolean('is_popular', false),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('plans.admin')
            ->with('success', 'Plano atualizado com sucesso!');
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(Plan $plan)
    {
        $this->authorize('delete', $plan);
        
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return redirect()->route('plans.admin')
                ->with('error', 'Não é possível excluir um plano que possui assinaturas ativas.');
        }

        $plan->delete();

        return redirect()->route('plans.admin')
            ->with('success', 'Plano excluído com sucesso!');
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
