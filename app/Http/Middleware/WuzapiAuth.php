<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WuzapiAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Pega o token do header
        $token = $request->header('Authorization') ?? $request->header('token');

        // Token que definiu no .env
        $validToken = env('WUZAPI_ADMIN_TOKEN', 'seu_token_aqui');

        if ($token !== $validToken) {
            return response()->json([
                'success' => false,
                'error' => 'unauthorized',
                'code' => 401
            ], 401);
        }

        return $next($request);
    }
}
