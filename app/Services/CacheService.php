<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache duration constants (in seconds)
     */
    const DURATION_SHORT = 300;    // 5 minutes
    const DURATION_MEDIUM = 1800;  // 30 minutes
    const DURATION_LONG = 3600;    // 1 hour
    const DURATION_VERY_LONG = 86400; // 24 hours

    /**
     * Cache API response with automatic key generation
     */
    public static function rememberApiResponse(string $endpoint, callable $callback, int $duration = self::DURATION_MEDIUM): mixed
    {
        $cacheKey = 'api_response_' . md5($endpoint);
        
        return Cache::remember($cacheKey, $duration, function () use ($callback, $endpoint) {
            try {
                $result = $callback();
                
                // Log successful API calls for monitoring
                Log::info('API Response Cached', [
                    'endpoint' => $endpoint,
                    'cache_key' => 'api_response_' . md5($endpoint),
                    'success' => true
                ]);
                
                return $result;
            } catch (\Exception $e) {
                Log::error('API Call Failed', [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
                
                throw $e;
            }
        });
    }

    /**
     * Cache user-specific data
     */
    public static function rememberUserData(int $userId, string $key, callable $callback, int $duration = self::DURATION_MEDIUM): mixed
    {
        $cacheKey = "user_data_{$userId}_{$key}";
        
        return Cache::remember($cacheKey, $duration, $callback);
    }

    /**
     * Clear user-specific cache
     */
    public static function clearUserCache(int $userId, ?string $pattern = null): void
    {
        if ($pattern) {
            $cacheKey = "user_data_{$userId}_{$pattern}";
            Cache::forget($cacheKey);
        } else {
            // Clear all user-related cache
            $patterns = [
                "user_data_{$userId}_*",
                "dashboard_stats_user_{$userId}",
                "access_status_user_{$userId}",
                "user_stats_{$userId}",
            ];
            
            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }
        }
    }

    /**
     * Cache with tags for easier invalidation
     */
    public static function rememberWithTags(array $tags, string $key, callable $callback, int $duration = self::DURATION_MEDIUM): mixed
    {
        return Cache::tags($tags)->remember($key, $duration, $callback);
    }

    /**
     * Invalidate cache by tags
     */
    public static function invalidateByTags(array $tags): void
    {
        Cache::tags($tags)->flush();
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        $stats = [];
        
        // Get cache driver info
        $driver = Cache::getStore();
        $stats['driver'] = get_class($driver);
        
        // For Redis, we can get more detailed stats
        if (method_exists($driver, 'connection')) {
            try {
                $connection = $driver->connection();
                if (method_exists($connection, 'info')) {
                    $info = $connection->info();
                    $stats['redis_info'] = [
                        'used_memory' => $info['used_memory_human'] ?? 'N/A',
                        'connected_clients' => $info['connected_clients'] ?? 'N/A',
                        'total_commands_processed' => $info['total_commands_processed'] ?? 'N/A',
                    ];
                }
            } catch (\Exception $e) {
                $stats['redis_error'] = $e->getMessage();
            }
        }
        
        return $stats;
    }

    /**
     * Warm up frequently accessed cache
     */
    public static function warmUpCache(): void
    {
        Log::info('Starting cache warm-up process');
        
        // Warm up user stats for active users
        $activeUsers = \App\Models\User::where('is_active', true)
            ->where('last_login_at', '>', now()->subDays(7))
            ->get();
            
        foreach ($activeUsers as $user) {
            try {
                $user->getCachedStats();
                Log::info("Warmed up cache for user {$user->id}");
            } catch (\Exception $e) {
                Log::error("Failed to warm up cache for user {$user->id}: " . $e->getMessage());
            }
        }
        
        Log::info('Cache warm-up process completed');
    }
}
