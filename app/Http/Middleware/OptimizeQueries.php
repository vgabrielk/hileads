<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OptimizeQueries
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enable query logging for admin routes
        if ($request->is('admin/*')) {
            DB::enableQueryLog();
        }

        $response = $next($request);

        // Log slow queries for admin routes
        if ($request->is('admin/*')) {
            $queries = DB::getQueryLog();
            $slowQueries = array_filter($queries, function ($query) {
                return $query['time'] > 1000; // Queries taking more than 1 second
            });

            if (!empty($slowQueries)) {
                \Log::warning('Slow queries detected in admin area', [
                    'url' => $request->fullUrl(),
                    'queries' => $slowQueries
                ]);
            }
        }

        return $response;
    }
}
