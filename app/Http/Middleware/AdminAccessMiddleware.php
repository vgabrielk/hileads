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
        // Verifica se o utilizador tem acesso às funcionalidades (admin ou subscrição ativa)
        if (auth()->check() && !auth()->user()->hasFeatureAccess()) {
            return redirect()->route('plans.index')
                ->with('error', 'Precisa de uma subscrição ativa para aceder esta funcionalidade.');
        }

        return $next($request);
    }
}
