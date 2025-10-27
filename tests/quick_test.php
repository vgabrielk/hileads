<?php

/**
 * Quick Load Test
 * 
 * A simplified load test to quickly check system capacity
 */

require_once 'load_test.php';
require_once 'system_monitor.php';
require_once 'whatsapp_api_test.php';

class QuickLoadTest
{
    private $config;
    
    public function __construct()
    {
        $this->config = require 'config.php';
    }
    
    /**
     * Run a quick load test
     */
    public function runQuickTest()
    {
        echo "⚡ Quick Load Test - WhatsApp Mass Sending System\n";
        echo str_repeat("=", 60) . "\n\n";
        
        // Test 1: Basic system health
        echo "🏥 Testing system health...\n";
        $this->testSystemHealth();
        
        // Test 2: Light load (10 users)
        echo "\n👥 Testing light load (10 users)...\n";
        $this->testLightLoad();
        
        // Test 3: Medium load (25 users)
        echo "\n👥 Testing medium load (25 users)...\n";
        $this->testMediumLoad();
        
        // Test 4: WhatsApp API
        echo "\n📱 Testing WhatsApp API...\n";
        $this->testWhatsAppApi();
        
        // Generate quick report
        $this->generateQuickReport();
    }
    
    /**
     * Test basic system health
     */
    private function testSystemHealth()
    {
        $startTime = microtime(true);
        
        // Test database connection
        try {
            $pdo = new PDO(
                "mysql:host={$this->config['database']['host']};dbname={$this->config['database']['database']}",
                $this->config['database']['username'],
                $this->config['database']['password']
            );
            echo "   ✅ Database connection: OK\n";
        } catch (Exception $e) {
            echo "   ❌ Database connection: FAILED - " . $e->getMessage() . "\n";
            return false;
        }
        
        // Test web server response
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['test']['base_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode === 200 && empty($error)) {
            echo "   ✅ Web server: OK\n";
        } else {
            echo "   ❌ Web server: FAILED - HTTP {$httpCode}\n";
            return false;
        }
        
        // Test memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        echo "   📊 Memory usage: " . $this->formatBytes($memoryUsage) . " (Peak: " . $this->formatBytes($memoryPeak) . ")\n";
        
        $duration = microtime(true) - $startTime;
        echo "   ⏱️  Health check duration: " . number_format($duration * 1000, 2) . "ms\n";
        
        return true;
    }
    
    /**
     * Test light load (10 users)
     */
    private function testLightLoad()
    {
        $userCount = 10;
        $startTime = microtime(true);
        
        $processes = [];
        $pipes = [];
        
        // Create processes
        for ($i = 0; $i < $userCount; $i++) {
            $descriptorspec = [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"]
            ];
            
            $process = proc_open("php load_test.php --single-user --user-id={$i}", $descriptorspec, $pipes[$i]);
            
            if (is_resource($process)) {
                $processes[$i] = $process;
            }
        }
        
        // Wait for completion
        $completed = 0;
        $errors = 0;
        
        foreach ($processes as $i => $process) {
            $status = proc_get_status($process);
            if (!$status['running']) {
                $completed++;
                $exitCode = proc_close($process);
                if ($exitCode !== 0) {
                    $errors++;
                }
            }
        }
        
        $duration = microtime(true) - $startTime;
        $successRate = $completed / $userCount;
        
        echo "   👥 Users: {$completed}/{$userCount} completed\n";
        echo "   ⏱️  Duration: " . number_format($duration, 2) . "s\n";
        echo "   📈 Success rate: " . number_format($successRate * 100, 2) . "%\n";
        echo "   🚀 RPS: " . number_format($completed / $duration, 2) . "\n";
        
        return $successRate > 0.9; // 90% success rate
    }
    
    /**
     * Test medium load (25 users)
     */
    private function testMediumLoad()
    {
        $userCount = 25;
        $startTime = microtime(true);
        
        $processes = [];
        $pipes = [];
        
        // Create processes
        for ($i = 0; $i < $userCount; $i++) {
            $descriptorspec = [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"]
            ];
            
            $process = proc_open("php load_test.php --single-user --user-id={$i}", $descriptorspec, $pipes[$i]);
            
            if (is_resource($process)) {
                $processes[$i] = $process;
            }
        }
        
        // Wait for completion
        $completed = 0;
        $errors = 0;
        
        foreach ($processes as $i => $process) {
            $status = proc_get_status($process);
            if (!$status['running']) {
                $completed++;
                $exitCode = proc_close($process);
                if ($exitCode !== 0) {
                    $errors++;
                }
            }
        }
        
        $duration = microtime(true) - $startTime;
        $successRate = $completed / $userCount;
        
        echo "   👥 Users: {$completed}/{$userCount} completed\n";
        echo "   ⏱️  Duration: " . number_format($duration, 2) . "s\n";
        echo "   📈 Success rate: " . number_format($successRate * 100, 2) . "%\n";
        echo "   🚀 RPS: " . number_format($completed / $duration, 2) . "\n";
        
        return $successRate > 0.8; // 80% success rate
    }
    
    /**
     * Test WhatsApp API
     */
    private function testWhatsAppApi()
    {
        $tester = new WhatsAppApiTester($this->config['test']['base_url'], $this->config['whatsapp']['api_key']);
        
        // Test connection status
        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['test']['base_url'] . '/api/whatsapp/status');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->config['whatsapp']['api_key'],
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $duration = microtime(true) - $startTime;
        
        if ($httpCode === 200 && empty($error)) {
            echo "   ✅ WhatsApp API: OK (" . number_format($duration * 1000, 2) . "ms)\n";
        } else {
            echo "   ❌ WhatsApp API: FAILED - HTTP {$httpCode}\n";
        }
    }
    
    /**
     * Generate quick report
     */
    private function generateQuickReport()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 QUICK TEST SUMMARY\n";
        echo str_repeat("=", 60) . "\n\n";
        
        echo "🎯 System Status:\n";
        echo "   • Database: " . ($this->testSystemHealth() ? "✅ OK" : "❌ FAILED") . "\n";
        echo "   • Web Server: " . ($this->testSystemHealth() ? "✅ OK" : "❌ FAILED") . "\n";
        echo "   • Memory Usage: " . $this->formatBytes(memory_get_usage(true)) . "\n";
        echo "   • Peak Memory: " . $this->formatBytes(memory_get_peak_usage(true)) . "\n\n";
        
        echo "💡 Recommendations:\n";
        echo "   • Run full load test for detailed analysis\n";
        echo "   • Monitor system resources during peak usage\n";
        echo "   • Test with real WhatsApp API credentials\n";
        echo "   • Consider load balancing for high traffic\n\n";
        
        echo "🚀 Next Steps:\n";
        echo "   • Run: ./run_load_test.sh 50 60\n";
        echo "   • Check results in results/ directory\n";
        echo "   • Analyze performance bottlenecks\n";
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

// Run quick test if called directly
if (php_sapi_name() === 'cli') {
    $quickTest = new QuickLoadTest();
    $quickTest->runQuickTest();
}
