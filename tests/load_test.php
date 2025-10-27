<?php

/**
 * Load Test Script for WhatsApp Mass Sending System
 * 
 * This script simulates multiple concurrent users to test system limits
 * and identify bottlenecks before they cause issues in production.
 */

class LoadTester
{
    private $baseUrl;
    private $results = [];
    private $concurrentUsers = 0;
    private $maxUsers = 0;
    private $errors = [];
    private $startTime;
    
    public function __construct($baseUrl = 'http://127.0.0.1:8000')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->startTime = microtime(true);
    }
    
    /**
     * Run load test with specified number of concurrent users
     */
    public function runLoadTest($maxUsers = 50, $duration = 60)
    {
        echo "🚀 Starting Load Test...\n";
        echo "📊 Testing up to {$maxUsers} concurrent users for {$duration} seconds\n";
        echo "🌐 Base URL: {$this->baseUrl}\n\n";
        
        $this->maxUsers = $maxUsers;
        $endTime = time() + $duration;
        
        // Test different user loads incrementally
        $userLoads = [1, 5, 10, 20, 30, 50, 75, 100, 150, 200];
        
        foreach ($userLoads as $userCount) {
            if ($userCount > $maxUsers) break;
            
            echo "👥 Testing with {$userCount} concurrent users...\n";
            $this->testConcurrentUsers($userCount);
            
            // Check if we should stop due to high error rate
            if ($this->getErrorRate() > 0.1) { // 10% error rate
                echo "⚠️  High error rate detected (" . ($this->getErrorRate() * 100) . "%), stopping test\n";
                break;
            }
            
            // Small delay between tests
            sleep(2);
        }
        
        $this->generateReport();
    }
    
    /**
     * Test with specific number of concurrent users
     */
    private function testConcurrentUsers($userCount)
    {
        $this->concurrentUsers = $userCount;
        $processes = [];
        $pipes = [];
        
        // Create multiple processes to simulate concurrent users
        for ($i = 0; $i < $userCount; $i++) {
            $descriptorspec = [
                0 => ["pipe", "r"],  // stdin
                1 => ["pipe", "w"],  // stdout
                2 => ["pipe", "w"]   // stderr
            ];
            
            $process = proc_open("php " . __FILE__ . " --single-user --user-id={$i}", $descriptorspec, $pipes[$i]);
            
            if (is_resource($process)) {
                $processes[$i] = $process;
            }
        }
        
        // Wait for all processes to complete
        $startTime = microtime(true);
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
        
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        
        // Record results
        $this->results[] = [
            'users' => $userCount,
            'duration' => $duration,
            'completed' => $completed,
            'errors' => $errors,
            'error_rate' => $errors / $userCount,
            'requests_per_second' => $completed / $duration
        ];
        
        echo "   ✅ Completed: {$completed}/{$userCount} users\n";
        echo "   ⚡ Duration: " . number_format($duration, 2) . "s\n";
        echo "   📈 RPS: " . number_format($completed / $duration, 2) . "\n";
        echo "   ❌ Errors: {$errors} (" . number_format(($errors / $userCount) * 100, 1) . "%)\n\n";
    }
    
    /**
     * Simulate a single user session
     */
    public function simulateSingleUser($userId = 0)
    {
        $sessionId = 'user_' . $userId . '_' . time();
        $actions = [
            'login',
            'view_dashboard',
            'view_contacts',
            'create_campaign',
            'send_message',
            'view_campaigns'
        ];
        
        $totalActions = 0;
        $successfulActions = 0;
        
        foreach ($actions as $action) {
            $totalActions++;
            
            try {
                $result = $this->performAction($action, $sessionId);
                if ($result['success']) {
                    $successfulActions++;
                } else {
                    $this->errors[] = "User {$userId}: {$action} failed - {$result['error']}";
                }
            } catch (Exception $e) {
                $this->errors[] = "User {$userId}: {$action} exception - " . $e->getMessage();
            }
            
            // Random delay between actions (0.1 to 0.5 seconds)
            usleep(rand(100000, 500000));
        }
        
        return [
            'user_id' => $userId,
            'total_actions' => $totalActions,
            'successful_actions' => $successfulActions,
            'success_rate' => $successfulActions / $totalActions
        ];
    }
    
    /**
     * Perform a specific action
     */
    private function performAction($action, $sessionId)
    {
        $startTime = microtime(true);
        
        switch ($action) {
            case 'login':
                return $this->testLogin($sessionId);
                
            case 'view_dashboard':
                return $this->testDashboard($sessionId);
                
            case 'view_contacts':
                return $this->testContacts($sessionId);
                
            case 'create_campaign':
                return $this->testCreateCampaign($sessionId);
                
            case 'send_message':
                return $this->testSendMessage($sessionId);
                
            case 'view_campaigns':
                return $this->testViewCampaigns($sessionId);
                
            default:
                return ['success' => false, 'error' => 'Unknown action'];
        }
    }
    
    /**
     * Test login functionality
     */
    private function testLogin($sessionId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/cookies_{$sessionId}.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies_{$sessionId}.txt");
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return [
            'success' => $httpCode === 200 && empty($error),
            'error' => $error ?: "HTTP {$httpCode}",
            'response_time' => microtime(true) - $this->startTime
        ];
    }
    
    /**
     * Test dashboard access
     */
    private function testDashboard($sessionId)
    {
        return $this->makeRequest('/dashboard', $sessionId);
    }
    
    /**
     * Test contacts page
     */
    private function testContacts($sessionId)
    {
        return $this->makeRequest('/contacts', $sessionId);
    }
    
    /**
     * Test campaign creation
     */
    private function testCreateCampaign($sessionId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/mass-sendings/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies_{$sessionId}.txt");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'name' => 'Test Campaign ' . time(),
            'message' => 'Test message for load testing',
            'contact_ids' => json_encode([])
        ]));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return [
            'success' => $httpCode === 200 && empty($error),
            'error' => $error ?: "HTTP {$httpCode}",
            'response_time' => microtime(true) - $this->startTime
        ];
    }
    
    /**
     * Test message sending
     */
    private function testSendMessage($sessionId)
    {
        // Add 5 second delay before sending message
        sleep(5);
        return $this->makeRequest('/mass-sendings', $sessionId);
    }
    
    /**
     * Test viewing campaigns
     */
    private function testViewCampaigns($sessionId)
    {
        return $this->makeRequest('/mass-sendings', $sessionId);
    }
    
    /**
     * Make a generic HTTP request
     */
    private function makeRequest($endpoint, $sessionId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies_{$sessionId}.txt");
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return [
            'success' => $httpCode === 200 && empty($error),
            'error' => $error ?: "HTTP {$httpCode}",
            'response_time' => microtime(true) - $this->startTime
        ];
    }
    
    /**
     * Get current error rate
     */
    private function getErrorRate()
    {
        if (empty($this->results)) return 0;
        
        $totalErrors = array_sum(array_column($this->results, 'errors'));
        $totalUsers = array_sum(array_column($this->results, 'users'));
        
        return $totalUsers > 0 ? $totalErrors / $totalUsers : 0;
    }
    
    /**
     * Generate comprehensive test report
     */
    private function generateReport()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 LOAD TEST REPORT\n";
        echo str_repeat("=", 80) . "\n\n";
        
        echo "🎯 Test Summary:\n";
        echo "   • Total test duration: " . number_format(microtime(true) - $this->startTime, 2) . "s\n";
        echo "   • Maximum users tested: " . max(array_column($this->results, 'users')) . "\n";
        echo "   • Total errors: " . count($this->errors) . "\n";
        echo "   • Overall error rate: " . number_format($this->getErrorRate() * 100, 2) . "%\n\n";
        
        echo "📈 Performance by User Load:\n";
        echo str_repeat("-", 80) . "\n";
        echo sprintf("%-8s %-12s %-12s %-12s %-12s %-12s\n", 
            "Users", "Duration(s)", "Completed", "Errors", "Error%", "RPS");
        echo str_repeat("-", 80) . "\n";
        
        foreach ($this->results as $result) {
            echo sprintf("%-8d %-12.2f %-12d %-12d %-12.1f %-12.2f\n",
                $result['users'],
                $result['duration'],
                $result['completed'],
                $result['errors'],
                $result['error_rate'] * 100,
                $result['requests_per_second']
            );
        }
        
        echo "\n🔍 Recommendations:\n";
        $this->generateRecommendations();
        
        echo "\n⚠️  Errors encountered:\n";
        if (!empty($this->errors)) {
            foreach (array_slice($this->errors, 0, 10) as $error) {
                echo "   • {$error}\n";
            }
            if (count($this->errors) > 10) {
                echo "   • ... and " . (count($this->errors) - 10) . " more errors\n";
            }
        } else {
            echo "   • No errors detected! 🎉\n";
        }
        
        echo "\n" . str_repeat("=", 80) . "\n";
    }
    
    /**
     * Generate performance recommendations
     */
    private function generateRecommendations()
    {
        $maxRPS = max(array_column($this->results, 'requests_per_second'));
        $maxUsers = max(array_column($this->results, 'users'));
        $errorRate = $this->getErrorRate();
        
        echo "   • Maximum supported RPS: " . number_format($maxRPS, 2) . "\n";
        echo "   • Maximum concurrent users: " . $maxUsers . "\n";
        
        if ($errorRate > 0.05) {
            echo "   ⚠️  High error rate detected - consider optimizing database queries\n";
        }
        
        if ($maxRPS < 10) {
            echo "   ⚠️  Low RPS - consider adding caching or optimizing code\n";
        }
        
        if ($maxUsers < 50) {
            echo "   ⚠️  Low concurrent user support - consider horizontal scaling\n";
        }
        
        echo "   • Recommended max concurrent users: " . max(1, $maxUsers * 0.8) . "\n";
        echo "   • Recommended max RPS: " . number_format($maxRPS * 0.8, 2) . "\n";
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    if (isset($argv[1]) && $argv[1] === '--single-user') {
        // Single user simulation
        $userId = isset($argv[3]) ? (int)$argv[3] : 0;
        $tester = new LoadTester();
        $result = $tester->simulateSingleUser($userId);
        echo json_encode($result);
    } else {
        // Full load test
        $maxUsers = isset($argv[1]) ? (int)$argv[1] : 50;
        $duration = isset($argv[2]) ? (int)$argv[2] : 60;
        
        $tester = new LoadTester();
        $tester->runLoadTest($maxUsers, $duration);
    }
}
