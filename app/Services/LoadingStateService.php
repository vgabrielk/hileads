<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LoadingStateService
{
    /**
     * Set loading state for a specific operation
     */
    public static function setLoading(string $operation, int $userId, int $ttl = 300): void
    {
        $key = "loading_state_{$operation}_{$userId}";
        Cache::put($key, [
            'loading' => true,
            'started_at' => now()->toISOString(),
            'operation' => $operation
        ], $ttl);
    }

    /**
     * Clear loading state for a specific operation
     */
    public static function clearLoading(string $operation, int $userId): void
    {
        $key = "loading_state_{$operation}_{$userId}";
        Cache::forget($key);
    }

    /**
     * Check if operation is loading
     */
    public static function isLoading(string $operation, int $userId): bool
    {
        $key = "loading_state_{$operation}_{$userId}";
        $state = Cache::get($key);
        
        return $state && ($state['loading'] ?? false);
    }

    /**
     * Get loading state information
     */
    public static function getLoadingState(string $operation, int $userId): ?array
    {
        $key = "loading_state_{$operation}_{$userId}";
        return Cache::get($key);
    }

    /**
     * Set progress for a long-running operation
     */
    public static function setProgress(string $operation, int $userId, int $current, int $total, string $message = ''): void
    {
        $key = "loading_state_{$operation}_{$userId}";
        $state = Cache::get($key, []);
        
        $state['loading'] = true;
        $state['progress'] = [
            'current' => $current,
            'total' => $total,
            'percentage' => $total > 0 ? round(($current / $total) * 100, 2) : 0,
            'message' => $message,
            'updated_at' => now()->toISOString()
        ];
        
        Cache::put($key, $state, 300);
    }

    /**
     * Get progress for a long-running operation
     */
    public static function getProgress(string $operation, int $userId): ?array
    {
        $state = self::getLoadingState($operation, $userId);
        return $state['progress'] ?? null;
    }

    /**
     * Set error state for an operation
     */
    public static function setError(string $operation, int $userId, string $message): void
    {
        $key = "loading_state_{$operation}_{$userId}";
        Cache::put($key, [
            'loading' => false,
            'error' => true,
            'message' => $message,
            'updated_at' => now()->toISOString()
        ], 300);
    }

    /**
     * Set success state for an operation
     */
    public static function setSuccess(string $operation, int $userId, string $message = ''): void
    {
        $key = "loading_state_{$operation}_{$userId}";
        Cache::put($key, [
            'loading' => false,
            'success' => true,
            'message' => $message,
            'updated_at' => now()->toISOString()
        ], 60); // Keep success state for 1 minute
    }

    /**
     * Clean up expired loading states
     */
    public static function cleanupExpiredStates(): void
    {
        // This would be called by a scheduled command
        // Implementation depends on cache driver
    }
}
