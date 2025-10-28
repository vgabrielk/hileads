<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Plan;
use App\Services\StripeService;

class SubscriptionController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display a listing of the user's subscriptions.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Se o utilizador é admin, mostra mensagem especial
        if ($user->isAdmin()) {
            $subscriptions = collect(); // Lista vazia para admins
            return view('subscriptions.index', compact('subscriptions'))->with('admin_message', true);
        }
        
        // Procurar apenas uma subscription por plano (a mais recente)
        $subscriptions = $user->subscriptions()
            ->with('plan')
            ->whereIn('id', function($query) use ($user) {
                $query->selectRaw('MAX(id)')
                    ->from('subscriptions')
                    ->where('user_id', $user->id)
                    ->groupBy('plan_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Display the specified subscription.
     */
    public function show(Subscription $subscription)
    {
        // Ensure user can only view their own subscriptions
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        $subscription->load('plan');

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Subscription $subscription)
    {
        // Ensure user can only cancel their own subscriptions
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }

        if ($subscription->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Esta subscrição não está ativa.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'metadata' => array_merge($subscription->metadata ?? [], [
                'cancelled_by_user' => true,
                'cancelled_at' => now()->toISOString()
            ])
        ]);

        return redirect()->back()
            ->with('success', 'Assinatura cancelada com sucesso.');
    }

    /**
     * Handle Bestfy webhook.
     */
    public function webhook(Request $request)
    {
        try {
            // Log de auditoria para todas as tentativas de webhook
            \Log::info('Bestfy webhook received', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
                'data_size' => strlen($request->getContent())
            ]);

            // Validar estrutura do payload
            $data = $request->all();
            if (!$this->validateWebhookPayload($data)) {
                \Log::warning('Bestfy webhook: Invalid payload structure', [
                    'ip' => $request->ip(),
                    'payload' => $data
                ]);
                
                return response()->json(['error' => 'Invalid payload'], 400);
            }

            // Verificar se já processamos este webhook (prevenir duplicação)
            $transactionId = $data['transaction']['id'] ?? null;
            if ($transactionId && $this->isWebhookAlreadyProcessed($transactionId)) {
                \Log::info('Bestfy webhook: Already processed', [
                    'transaction_id' => $transactionId,
                    'ip' => $request->ip()
                ]);
                
                return response()->json(['status' => 'already_processed']);
            }

            // Processar webhook
            // $this->bestfyService->processWebhook($data);
            
            // TODO: Implementar processamento de webhook do Stripe
            
            // Marcar como processado
            if ($transactionId) {
                $this->markWebhookAsProcessed($transactionId);
            }
            
            \Log::info('Bestfy webhook processed successfully', [
                'transaction_id' => $transactionId,
                'ip' => $request->ip()
            ]);
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Bestfy webhook error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Validate webhook payload structure.
     */
    private function validateWebhookPayload(array $data): bool
    {
        // Verificar estrutura mínima necessária
        if (empty($data)) {
            return false;
        }

        // Verificar se tem checkout e transaction
        if (!isset($data['checkout']) || !isset($data['transaction'])) {
            return false;
        }

        // Verificar se tem IDs necessários
        if (empty($data['checkout']['id']) || empty($data['transaction']['id'])) {
            return false;
        }

        // Verificar se tem status da transação
        if (empty($data['transaction']['status'])) {
            return false;
        }

        return true;
    }

    /**
     * Check if webhook was already processed.
     */
    private function isWebhookAlreadyProcessed(string $transactionId): bool
    {
        $cacheKey = "webhook_processed_{$transactionId}";
        return \Cache::has($cacheKey);
    }

    /**
     * Mark webhook as processed.
     */
    private function markWebhookAsProcessed(string $transactionId): void
    {
        $cacheKey = "webhook_processed_{$transactionId}";
        \Cache::put($cacheKey, true, 86400); // 24 horas
    }

    /**
     * Check subscription status for AJAX requests.
     */
    public function checkStatus()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $activeSubscription = $user->activeSubscription()->first();
        
        if ($activeSubscription) {
            return response()->json([
                'has_subscription' => true,
                'subscription' => [
                    'id' => $activeSubscription->id,
                    'status' => $activeSubscription->status,
                    'plan_name' => $activeSubscription->plan->name,
                    'expires_at' => $activeSubscription->expires_at->toISOString(),
                    'is_active' => $activeSubscription->isActive()
                ]
            ]);
        }

        return response()->json([
            'has_subscription' => false,
            'subscription' => null
        ]);
    }

    /**
     * Show success page for completed subscription checkout.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('subscriptions.error')
                ->with('error', 'Sessão de checkout inválida.');
        }

        try {
            $session = $this->stripeService->getCheckoutSession($sessionId);

            if ($session->payment_status === 'paid') {
                // Procurar subscrição pendente relacionada a esta sessão
                $subscription = \App\Models\Subscription::where('stripe_session_id', $sessionId)
                    ->where('status', 'pending')
                    ->first();

                // Se encontrou subscrição pendente, ativar automaticamente
                if ($subscription) {
                    $subscription->update([
                        'status' => 'active',
                        'stripe_customer_id' => $session->customer ?? $subscription->stripe_customer_id,
                        'stripe_subscription_id' => $session->subscription ?? $subscription->stripe_subscription_id,
                        'starts_at' => now(),
                        'expires_at' => $this->calculateExpirationDate($subscription->plan),
                        'metadata' => array_merge($subscription->metadata ?? [], [
                            'checkout_completed_at' => now()->toISOString(),
                            'session_id' => $session->id,
                            'payment_status' => $session->payment_status,
                            'auto_activated_at_success_page' => true
                        ])
                    ]);

                    \Log::info('Subscription activated automatically at success page', [
                        'subscription_id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                        'plan_id' => $subscription->plan_id,
                        'session_id' => $sessionId
                    ]);
                }

                // Mostrar página de sucesso com informações da sessão
                return view('subscriptions.success', [
                    'sessionId' => $sessionId,
                    'session' => $session,
                    'subscription' => $subscription ?? null
                ]);
            } else {
                return redirect()->route('subscriptions.error')
                    ->with('error', 'Pagamento não foi concluído. Tente novamente.');
            }
        } catch (\Exception $e) {
            \Log::error('Error in subscription success page', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return view('subscriptions.success', [
                'sessionId' => $sessionId,
                'session' => null,
                'subscription' => null
            ]);
        }
    }

    /**
     * Show error page for failed subscription checkout.
     */
    public function error(Request $request)
    {
        $error = $request->get('error', 'Erro ao processar o pagamento.');
        
        return view('subscriptions.error', compact('error'));
    }

    /**
     * Calculate expiration date based on plan interval.
     */
    private function calculateExpirationDate(Plan $plan): \DateTime
    {
        $now = now();

        if ($plan->interval === 'monthly') {
            return $now->copy()->addMonths($plan->interval_count ?? 1);
        } elseif ($plan->interval === 'yearly') {
            return $now->copy()->addYears($plan->interval_count ?? 1);
        }

        // Default to monthly if interval is not recognized
        return $now->copy()->addMonth();
    }
}
