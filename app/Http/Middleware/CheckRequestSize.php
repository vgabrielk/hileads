<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRequestSize
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar tamanho do request apenas para rotas de mídia
        if ($request->is('mass-sendings') && $request->isMethod('POST')) {
            $contentLength = $request->header('Content-Length');
            $maxSize = 25 * 1024 * 1024; // 25MB
            
            if ($contentLength && $contentLength > $maxSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request muito grande. Tamanho máximo permitido: 25MB.',
                    'error_code' => 'REQUEST_TOO_LARGE',
                    'details' => [
                        'content_length' => $contentLength,
                        'max_size' => $maxSize
                    ]
                ], 413);
            }
        }

        return $next($request);
    }
}
