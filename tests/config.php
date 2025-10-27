<?php

/**
 * Load Test Configuration
 * 
 * Configuration file for load testing the WhatsApp Mass Sending System
 */

return [
    // Test Configuration
    'test' => [
        'base_url' => env('APP_URL', 'http://127.0.0.1:8000'),
        'max_users' => env('LOAD_TEST_MAX_USERS', 50),
        'test_duration' => env('LOAD_TEST_DURATION', 60), // seconds
        'monitor_interval' => env('LOAD_TEST_MONITOR_INTERVAL', 1), // seconds
    ],
    
    // Database Configuration
    'database' => [
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'hileads'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
    ],
    
    // WhatsApp API Configuration
    'whatsapp' => [
        'api_key' => env('WUZAPI_API_KEY', 'test-key'),
        'base_url' => env('WUZAPI_BASE_URL', 'https://api.wuzapi.com'),
        'timeout' => env('WUZAPI_TIMEOUT', 30),
        'max_retries' => env('WUZAPI_MAX_RETRIES', 3),
    ],
    
    // Load Test Scenarios
    'scenarios' => [
        'light_load' => [
            'users' => 10,
            'duration' => 30,
            'description' => 'Light load test for basic functionality'
        ],
        'medium_load' => [
            'users' => 50,
            'duration' => 60,
            'description' => 'Medium load test for normal usage'
        ],
        'heavy_load' => [
            'users' => 100,
            'duration' => 120,
            'description' => 'Heavy load test for stress testing'
        ],
        'extreme_load' => [
            'users' => 200,
            'duration' => 180,
            'description' => 'Extreme load test to find breaking point'
        ]
    ],
    
    // Performance Thresholds
    'thresholds' => [
        'max_response_time' => 5.0, // seconds
        'min_success_rate' => 0.95, // 95%
        'max_memory_usage' => 512 * 1024 * 1024, // 512MB
        'max_cpu_usage' => 80, // 80%
        'max_db_connections' => 50,
    ],
    
    // Test Data
    'test_data' => [
        'test_phones' => [
            '5511999999999',
            '5511888888888',
            '5511777777777',
            '5511666666666',
            '5511555555555'
        ],
        'test_messages' => [
            'Test message for load testing - {timestamp}',
            'Bulk test message - {user_id}',
            'Performance test message - {session_id}',
            'Load test message - {random}',
            'System test message - {counter}'
        ],
        'test_campaigns' => [
            'Load Test Campaign 1',
            'Performance Test Campaign 2',
            'Stress Test Campaign 3',
            'Capacity Test Campaign 4',
            'System Test Campaign 5'
        ]
    ],
    
    // Monitoring Configuration
    'monitoring' => [
        'log_file' => '/tmp/load_test_monitor.log',
        'metrics_interval' => 1, // seconds
        'alert_thresholds' => [
            'memory_usage' => 0.8, // 80%
            'cpu_usage' => 0.8, // 80%
            'error_rate' => 0.1, // 10%
            'response_time' => 10.0 // 10 seconds
        ]
    ],
    
    // Report Configuration
    'reports' => [
        'output_dir' => 'results',
        'include_charts' => true,
        'include_detailed_logs' => true,
        'email_reports' => false,
        'email_recipients' => []
    ]
];
