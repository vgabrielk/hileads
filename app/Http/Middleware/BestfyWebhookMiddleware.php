<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BestfyWebhookMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Rate limiting para webhooks
        $clientIp = $request->ip();
        $cacheKey = "bestfy_webhook_rate_limit_{$clientIp}";
        
        if (Cache::has($cacheKey)) {
            $attempts = Cache::get($cacheKey, 0);
            if ($attempts > 10) { // Máximo 10 tentativas por minuto
                Log::warning('Bestfy webhook rate limit exceeded', [
                    'ip' => $clientIp,
                    'attempts' => $attempts
                ]);
                
                return response()->json(['error' => 'Rate limit exceeded'], 429);
            }
            Cache::increment($cacheKey);
        } else {
            Cache::put($cacheKey, 1, 60); // 1 minuto
        }

        // Log da requisição para auditoria
        Log::info('Bestfy webhook received', [
            'ip' => $clientIp,
            'user_agent' => $request->userAgent(),
            'content_type' => $request->header('Content-Type'),
            'data_size' => strlen($request->getContent())
        ]);

        // Verificar se é uma requisição POST
        if (!$request->isMethod('post')) {
            Log::warning('Bestfy webhook: Invalid method', [
                'method' => $request->method(),
                'ip' => $clientIp
            ]);
            
            return response()->json(['error' => 'Method not allowed'], 405);
        }

        // Verificar Content-Type
        if (!$request->isJson()) {
            Log::warning('Bestfy webhook: Invalid content type', [
                'content_type' => $request->header('Content-Type'),
                'ip' => $clientIp
            ]);
            
            return response()->json(['error' => 'Invalid content type'], 400);
        }

        // Validar estrutura básica do payload
        $data = $request->all();
        if (empty($data) || !isset($data['checkout']) || !isset($data['transaction'])) {
            Log::warning('Bestfy webhook: Invalid payload structure', [
                'ip' => $clientIp,
                'payload_keys' => array_keys($data)
            ]);
            
            return response()->json(['error' => 'Invalid payload structure'], 400);
        }

        return $next($request);
    }
}
