<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $decayMinutes * 60
            ], 429);
        }
        
        Cache::put($key, $attempts + 1, $decayMinutes * 60);
        
        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - $attempts - 1));
        $response->headers->set('X-RateLimit-Reset', now()->addMinutes($decayMinutes)->timestamp);
        
        return $response;
    }

    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        $ip = $request->ip();
        $route = $request->route()?->getName() ?? $request->path();
        
        if ($user) {
            return "rate_limit_user_{$user->id}_{$route}";
        }
        
        return "rate_limit_ip_{$ip}_{$route}";
    }
}
