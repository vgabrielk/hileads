<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user) {
            Log::warning('Subscription security check failed: No authenticated user');
            return redirect()->route('login');
        }

        // Admins sempre têm acesso
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verificar se o usuário tem assinatura ativa
        $activeSubscription = $user->activeSubscription()->first();
        
        if (!$activeSubscription) {
            Log::info('Subscription security check failed: No active subscription', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'request_uri' => $request->getRequestUri()
            ]);
            
            return redirect()->route('plans.index')
                ->with('error', 'Você precisa de uma assinatura ativa para acessar esta funcionalidade.');
        }

        // Verificar se a assinatura não expirou
        if ($activeSubscription->isExpired()) {
            Log::warning('Subscription security check failed: Subscription expired', [
                'user_id' => $user->id,
                'subscription_id' => $activeSubscription->id,
                'expires_at' => $activeSubscription->expires_at,
                'request_uri' => $request->getRequestUri()
            ]);
            
            return redirect()->route('plans.index')
                ->with('error', 'Sua assinatura expirou. Renove para continuar usando o sistema.');
        }

        // Verificar se a assinatura não foi cancelada
        if ($activeSubscription->status !== 'active') {
            Log::warning('Subscription security check failed: Subscription not active', [
                'user_id' => $user->id,
                'subscription_id' => $activeSubscription->id,
                'status' => $activeSubscription->status,
                'request_uri' => $request->getRequestUri()
            ]);
            
            return redirect()->route('plans.index')
                ->with('error', 'Sua assinatura não está ativa. Entre em contato com o suporte.');
        }

        // Log de acesso autorizado para auditoria
        Log::info('Subscription security check passed', [
            'user_id' => $user->id,
            'subscription_id' => $activeSubscription->id,
            'plan_id' => $activeSubscription->plan_id,
            'expires_at' => $activeSubscription->expires_at,
            'request_uri' => $request->getRequestUri()
        ]);

        return $next($request);
    }
}
