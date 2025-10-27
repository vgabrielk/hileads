<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de autenticação não fornecido.'
            ], 401);
        }

        // Remove 'Bearer ' prefix if present
        $token = str_replace('Bearer ', '', $token);

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token de autenticação inválido.'
            ], 401);
        }

        // Check if user is admin for admin routes
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem acessar este recurso.'
            ], 403);
        }

        // Set the authenticated user
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
