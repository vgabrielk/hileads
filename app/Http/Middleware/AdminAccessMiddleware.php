<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário tem acesso às funcionalidades (admin ou assinatura ativa)
        if (auth()->check() && !auth()->user()->hasFeatureAccess()) {
            return redirect()->route('plans.index')
                ->with('error', 'Você precisa de uma assinatura ativa para acessar esta funcionalidade.');
        }

        return $next($request);
    }
}
