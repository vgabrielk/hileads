<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminSubscriptionController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display a listing of all subscriptions for admin.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('user_search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_search . '%')
                  ->orWhere('email', 'like', '%' . $request->user_search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);
        $plans = Plan::where('is_active', true)->get();
        $statuses = ['active', 'pending', 'cancelled', 'expired', 'failed'];

        // Estatísticas
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'failed' => Subscription::where('status', 'failed')->count(),
            'revenue' => Subscription::where('status', 'active')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->sum('plans.price'),
        ];

        return view('admin.subscriptions.index', compact(
            'subscriptions', 
            'plans', 
            'statuses', 
            'stats'
        ));
    }

    /**
     * Display the specified subscription.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);
        
        // Procurar dados do Stripe se disponível
        $stripeData = null;
        if ($subscription->stripe_subscription_id) {
            try {
                $stripeData = $this->stripeService->getSubscription($subscription->stripe_subscription_id);
            } catch (\Exception $e) {
                Log::warning('Failed to fetch Stripe subscription data', [
                    'subscription_id' => $subscription->id,
                    'stripe_subscription_id' => $subscription->stripe_subscription_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('admin.subscriptions.show', compact('subscription', 'stripeData'));
    }

    /**
     * Show the form for editing the specified subscription.
     */
    public function edit(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);
        $plans = Plan::where('is_active', true)->get();
        $statuses = ['active', 'pending', 'cancelled', 'expired', 'failed'];

        return view('admin.subscriptions.edit', compact('subscription', 'plans', 'statuses'));
    }

    /**
     * Update the specified subscription.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'status' => 'required|in:active,pending,cancelled,expired,failed',
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $oldStatus = $subscription->status;
            $oldPlanId = $subscription->plan_id;

            $subscription->update([
                'status' => $request->status,
                'plan_id' => $request->plan_id,
                'starts_at' => $request->starts_at,
                'expires_at' => $request->expires_at,
                'notes' => $request->notes,
            ]);

            // Se mudou o plano, atualizar o preço
            if ($oldPlanId != $request->plan_id) {
                $newPlan = Plan::find($request->plan_id);
                $subscription->update([
                    'amount' => $newPlan->price,
                    'amount_cents' => $newPlan->price_cents,
                ]);
            }

            // Se mudou para ativo, sincronizar com Stripe se necessário
            if ($oldStatus !== 'active' && $request->status === 'active' && $subscription->stripe_subscription_id) {
                try {
                    $this->stripeService->activateSubscription($subscription->stripe_subscription_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to activate Stripe subscription', [
                        'subscription_id' => $subscription->id,
                        'stripe_subscription_id' => $subscription->stripe_subscription_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Se mudou para cancelado, cancelar no Stripe se necessário
            if ($oldStatus !== 'cancelled' && $request->status === 'cancelled' && $subscription->stripe_subscription_id) {
                try {
                    $this->stripeService->cancelSubscription($subscription);
                } catch (\Exception $e) {
                    Log::warning('Failed to cancel Stripe subscription', [
                        'subscription_id' => $subscription->id,
                        'stripe_subscription_id' => $subscription->stripe_subscription_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Admin updated subscription', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'old_plan_id' => $oldPlanId,
                'new_plan_id' => $request->plan_id,
                'admin_id' => auth()->id()
            ]);

            return redirect()->route('admin.subscriptions.show', $subscription)
                ->with('success', 'Assinatura atualizada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to update subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao atualizar assinatura: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Subscription $subscription)
    {
        try {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Cancelar no Stripe se necessário
            if ($subscription->stripe_subscription_id) {
                try {
                    $this->stripeService->cancelSubscription($subscription);
                } catch (\Exception $e) {
                    Log::warning('Failed to cancel Stripe subscription', [
                        'subscription_id' => $subscription->id,
                        'stripe_subscription_id' => $subscription->stripe_subscription_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Admin cancelled subscription', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', 'Assinatura cancelada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao cancelar assinatura: ' . $e->getMessage());
        }
    }

    /**
     * Reactivate a cancelled subscription.
     */
    public function reactivate(Subscription $subscription)
    {
        try {
            $subscription->update([
                'status' => 'active',
                'cancelled_at' => null,
                'expires_at' => Carbon::now()->addMonth(), // Renovar por mais 1 mês
            ]);

            // Reativar no Stripe se necessário
            if ($subscription->stripe_subscription_id) {
                try {
                    $this->stripeService->activateSubscription($subscription->stripe_subscription_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to reactivate Stripe subscription', [
                        'subscription_id' => $subscription->id,
                        'stripe_subscription_id' => $subscription->stripe_subscription_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Admin reactivated subscription', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', 'Assinatura reativada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to reactivate subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao reativar assinatura: ' . $e->getMessage());
        }
    }

    /**
     * Delete a subscription.
     */
    public function destroy(Subscription $subscription)
    {
        try {
            // Cancelar no Stripe se necessário
            if ($subscription->stripe_subscription_id) {
                try {
                    $this->stripeService->cancelSubscription($subscription);
                } catch (\Exception $e) {
                    Log::warning('Failed to cancel Stripe subscription before deletion', [
                        'subscription_id' => $subscription->id,
                        'stripe_subscription_id' => $subscription->stripe_subscription_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $subscriptionId = $subscription->id;
            $userId = $subscription->user_id;
            
            $subscription->delete();

            Log::info('Admin deleted subscription', [
                'subscription_id' => $subscriptionId,
                'user_id' => $userId,
                'admin_id' => auth()->id()
            ]);

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Assinatura eliminada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to delete subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao eliminar assinatura: ' . $e->getMessage());
        }
    }

    /**
     * Sync subscription with Stripe.
     */
    public function sync(Subscription $subscription)
    {
        if (!$subscription->stripe_subscription_id) {
            return redirect()->back()
                ->with('error', 'Esta assinatura não possui ID do Stripe para sincronizar.');
        }

        try {
            $stripeSubscription = $this->stripeService->getSubscription($subscription->stripe_subscription_id);
            
            $subscription->update([
                'status' => $stripeSubscription->status,
                'stripe_status' => $stripeSubscription->status,
                'current_period_start' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            ]);

            Log::info('Admin synced subscription with Stripe', [
                'subscription_id' => $subscription->id,
                'stripe_subscription_id' => $subscription->stripe_subscription_id,
                'new_status' => $stripeSubscription->status,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', 'Assinatura sincronizada com o Stripe com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to sync subscription with Stripe', [
                'subscription_id' => $subscription->id,
                'stripe_subscription_id' => $subscription->stripe_subscription_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao sincronizar com o Stripe: ' . $e->getMessage());
        }
    }
}