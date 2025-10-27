<?php

/**
 * Queue Memory Optimization Configuration
 * 
 * This file contains memory optimization settings for queue workers
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Memory Optimization Settings
    |--------------------------------------------------------------------------
    |
    | These settings help optimize memory usage for queue workers
    |
    */
    
    'memory_limit' => env('QUEUE_MEMORY_LIMIT', '256M'),
    'max_execution_time' => env('QUEUE_MAX_EXECUTION_TIME', 300), // 5 minutes
    'batch_size' => env('QUEUE_BATCH_SIZE', 20),
    'batch_delay' => env('QUEUE_BATCH_DELAY', 5), // seconds
    
    /*
    |--------------------------------------------------------------------------
    | Garbage Collection Settings
    |--------------------------------------------------------------------------
    |
    | Settings for automatic garbage collection
    |
    */
    
    'gc_probability' => env('QUEUE_GC_PROBABILITY', 1),
    'gc_divisor' => env('QUEUE_GC_DIVISOR', 100),
    
    /*
    |--------------------------------------------------------------------------
    | Memory Monitoring
    |--------------------------------------------------------------------------
    |
    | Settings for monitoring memory usage
    |
    */
    
    'memory_warning_threshold' => env('QUEUE_MEMORY_WARNING', 80), // percentage
    'memory_critical_threshold' => env('QUEUE_MEMORY_CRITICAL', 90), // percentage
];
