<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        // Enable query logging for admin routes and performance monitoring
        $enableLogging = $request->is('admin/*') || config('app.debug');
        
        if ($enableLogging) {
            DB::enableQueryLog();
        }

        $startTime = microtime(true);
        $response = $next($request);
        $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        if ($enableLogging) {
            $queries = DB::getQueryLog();
            $queryCount = count($queries);
            
            // Log performance metrics
            Log::info('Request Performance', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time_ms' => round($executionTime, 2),
                'query_count' => $queryCount,
                'memory_usage_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ]);

            // Log slow queries
            $slowQueries = array_filter($queries, function ($query) {
                return $query['time'] > 1000; // Queries taking more than 1 second
            });

            if (!empty($slowQueries)) {
                Log::warning('Slow queries detected', [
                    'url' => $request->fullUrl(),
                    'slow_queries' => $slowQueries,
                    'total_queries' => $queryCount
                ]);
            }

            // Log queries with N+1 potential
            if ($queryCount > 20) {
                Log::warning('High query count detected - potential N+1 issue', [
                    'url' => $request->fullUrl(),
                    'query_count' => $queryCount,
                    'queries' => $queries
                ]);
            }
        }

        return $response;
    }
}
