<?php

/**
 * WhatsApp API Load Test
 * 
 * Tests the WhatsApp API integration under load to identify limits
 */

class WhatsAppApiTester
{
    private $baseUrl;
    private $apiKey;
    private $results = [];
    
    public function __construct($baseUrl = 'http://127.0.0.1:8000', $apiKey = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey ?: 'test-key';
    }
    
    /**
     * Run comprehensive WhatsApp API tests
     */
    public function runApiTests()
    {
        echo "📱 WhatsApp API Load Test\n";
        echo str_repeat("=", 50) . "\n\n";
        
        $this->testConnectionStatus();
        $this->testMessageSending();
        $this->testBulkMessaging();
        $this->testRateLimits();
        $this->testConcurrentSessions();
        
        $this->generateApiReport();
    }
    
    /**
     * Test connection status endpoint
     */
    private function testConnectionStatus()
    {
        echo "🔌 Testing connection status...\n";
        
        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/api/whatsapp/status');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $duration = microtime(true) - $startTime;
        
        $this->results['connection_status'] = [
            'success' => $httpCode === 200 && empty($error),
            'response_time' => $duration,
            'http_code' => $httpCode,
            'error' => $error
        ];
        
        echo "   " . ($this->results['connection_status']['success'] ? "✅" : "❌") . 
             " Response time: " . number_format($duration * 1000, 2) . "ms\n\n";
    }
    
    /**
     * Test single message sending
     */
    private function testMessageSending()
    {
        echo "📤 Testing message sending...\n";
        
        $testMessages = [
            ['phone' => '5511999999999', 'message' => 'Test message 1'],
            ['phone' => '5511888888888', 'message' => 'Test message 2'],
            ['phone' => '5511777777777', 'message' => 'Test message 3']
        ];
        
        $successCount = 0;
        $totalTime = 0;
        
        foreach ($testMessages as $message) {
            $startTime = microtime(true);
            
            // Add 5 second delay before sending each message
            sleep(5);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/api/whatsapp/send');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $duration = microtime(true) - $startTime;
            $totalTime += $duration;
            
            if ($httpCode === 200 && empty($error)) {
                $successCount++;
            }
            
            echo "   Message to {$message['phone']}: " . 
                 ($httpCode === 200 ? "✅" : "❌") . 
                 " (" . number_format($duration * 1000, 2) . "ms)\n";
        }
        
        $this->results['message_sending'] = [
            'success_rate' => $successCount / count($testMessages),
            'average_response_time' => $totalTime / count($testMessages),
            'total_messages' => count($testMessages),
            'successful_messages' => $successCount
        ];
        
        echo "   Success rate: " . number_format($this->results['message_sending']['success_rate'] * 100, 2) . "%\n\n";
    }
    
    /**
     * Test bulk messaging capabilities
     */
    private function testBulkMessaging()
    {
        echo "📢 Testing bulk messaging...\n";
        
        $bulkSizes = [10, 50, 100, 200];
        $bulkResults = [];
        
        foreach ($bulkSizes as $size) {
            echo "   Testing bulk size: {$size} messages\n";
            
            $messages = [];
            for ($i = 0; $i < $size; $i++) {
                $messages[] = [
                    'phone' => '5511' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'message' => "Bulk test message {$i}"
                ];
            }
            
            $startTime = microtime(true);
            
            // Add 5 second delay before sending bulk messages
            sleep(5);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/api/whatsapp/bulk-send');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['messages' => $messages]));
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $duration = microtime(true) - $startTime;
            
            $bulkResults[$size] = [
                'success' => $httpCode === 200 && empty($error),
                'response_time' => $duration,
                'messages_per_second' => $size / $duration,
                'http_code' => $httpCode,
                'error' => $error
            ];
            
            echo "     " . ($bulkResults[$size]['success'] ? "✅" : "❌") . 
                 " Time: " . number_format($duration, 2) . "s | " .
                 "Rate: " . number_format($bulkResults[$size]['messages_per_second'], 2) . " msg/s\n";
        }
        
        $this->results['bulk_messaging'] = $bulkResults;
        echo "\n";
    }
    
    /**
     * Test rate limits
     */
    private function testRateLimits()
    {
        echo "⏱️  Testing rate limits...\n";
        
        $requestsPerSecond = [1, 5, 10, 20, 50];
        $rateLimitResults = [];
        
        foreach ($requestsPerSecond as $rps) {
            echo "   Testing {$rps} requests per second\n";
            
            $successCount = 0;
            $errorCount = 0;
            $startTime = microtime(true);
            
            for ($i = 0; $i < $rps * 10; $i++) { // Test for 10 seconds
                $requestStart = microtime(true);
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/api/whatsapp/status');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $this->apiKey,
                    'Content-Type: application/json'
                ]);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                if ($httpCode === 200 && empty($error)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                
                // Wait to maintain the desired RPS
                $elapsed = microtime(true) - $requestStart;
                $waitTime = (1 / $rps) - $elapsed;
                if ($waitTime > 0) {
                    usleep($waitTime * 1000000);
                }
            }
            
            $totalTime = microtime(true) - $startTime;
            $actualRPS = ($successCount + $errorCount) / $totalTime;
            
            $rateLimitResults[$rps] = [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'success_rate' => $successCount / ($successCount + $errorCount),
                'actual_rps' => $actualRPS,
                'target_rps' => $rps
            ];
            
            echo "     Success rate: " . number_format($rateLimitResults[$rps]['success_rate'] * 100, 2) . 
                 "% | Actual RPS: " . number_format($actualRPS, 2) . "\n";
        }
        
        $this->results['rate_limits'] = $rateLimitResults;
        echo "\n";
    }
    
    /**
     * Test concurrent sessions
     */
    private function testConcurrentSessions()
    {
        echo "👥 Testing concurrent sessions...\n";
        
        $concurrentSessions = [1, 5, 10, 20];
        $sessionResults = [];
        
        foreach ($concurrentSessions as $sessionCount) {
            echo "   Testing {$sessionCount} concurrent sessions\n";
            
            $processes = [];
            $pipes = [];
            
            // Create multiple processes to simulate concurrent sessions
            for ($i = 0; $i < $sessionCount; $i++) {
                $descriptorspec = [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => ["pipe", "w"]
                ];
                
                $process = proc_open("php " . __FILE__ . " --single-session --session-id={$i}", $descriptorspec, $pipes[$i]);
                
                if (is_resource($process)) {
                    $processes[$i] = $process;
                }
            }
            
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
            
            $duration = microtime(true) - $startTime;
            
            $sessionResults[$sessionCount] = [
                'completed' => $completed,
                'errors' => $errors,
                'success_rate' => $completed / $sessionCount,
                'duration' => $duration,
                'sessions_per_second' => $completed / $duration
            ];
            
            echo "     Completed: {$completed}/{$sessionCount} | " .
                 "Success rate: " . number_format($sessionResults[$sessionCount]['success_rate'] * 100, 2) . "%\n";
        }
        
        $this->results['concurrent_sessions'] = $sessionResults;
        echo "\n";
    }
    
    /**
     * Simulate a single session
     */
    public function simulateSingleSession($sessionId = 0)
    {
        $actions = [
            'check_status',
            'send_message',
            'get_contacts',
            'send_bulk_message'
        ];
        
        $successCount = 0;
        $totalActions = count($actions);
        
        foreach ($actions as $action) {
            // Add 5 second delay before each action
            sleep(5);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/api/whatsapp/' . $action);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200 && empty($error)) {
                $successCount++;
            }
            
            // Small delay between actions
            usleep(rand(100000, 500000));
        }
        
        return [
            'session_id' => $sessionId,
            'success_count' => $successCount,
            'total_actions' => $totalActions,
            'success_rate' => $successCount / $totalActions
        ];
    }
    
    /**
     * Generate comprehensive API report
     */
    private function generateApiReport()
    {
        echo "\n📊 WHATSAPP API PERFORMANCE REPORT\n";
        echo str_repeat("=", 60) . "\n\n";
        
        // Connection status
        if (isset($this->results['connection_status'])) {
            $conn = $this->results['connection_status'];
            echo "🔌 CONNECTION STATUS:\n";
            echo "   • Success: " . ($conn['success'] ? "✅" : "❌") . "\n";
            echo "   • Response time: " . number_format($conn['response_time'] * 1000, 2) . "ms\n";
            echo "   • HTTP code: {$conn['http_code']}\n\n";
        }
        
        // Message sending
        if (isset($this->results['message_sending'])) {
            $msg = $this->results['message_sending'];
            echo "📤 MESSAGE SENDING:\n";
            echo "   • Success rate: " . number_format($msg['success_rate'] * 100, 2) . "%\n";
            echo "   • Average response time: " . number_format($msg['average_response_time'] * 1000, 2) . "ms\n";
            echo "   • Successful messages: {$msg['successful_messages']}/{$msg['total_messages']}\n\n";
        }
        
        // Bulk messaging
        if (isset($this->results['bulk_messaging'])) {
            echo "📢 BULK MESSAGING CAPACITY:\n";
            foreach ($this->results['bulk_messaging'] as $size => $result) {
                echo "   • {$size} messages: " . 
                     ($result['success'] ? "✅" : "❌") . 
                     " | " . number_format($result['messages_per_second'], 2) . " msg/s\n";
            }
            echo "\n";
        }
        
        // Rate limits
        if (isset($this->results['rate_limits'])) {
            echo "⏱️  RATE LIMIT ANALYSIS:\n";
            foreach ($this->results['rate_limits'] as $rps => $result) {
                echo "   • {$rps} RPS: " . number_format($result['success_rate'] * 100, 2) . 
                     "% success | " . number_format($result['actual_rps'], 2) . " actual RPS\n";
            }
            echo "\n";
        }
        
        // Concurrent sessions
        if (isset($this->results['concurrent_sessions'])) {
            echo "👥 CONCURRENT SESSION CAPACITY:\n";
            foreach ($this->results['concurrent_sessions'] as $sessions => $result) {
                echo "   • {$sessions} sessions: " . number_format($result['success_rate'] * 100, 2) . 
                     "% success | " . number_format($result['sessions_per_second'], 2) . " sessions/s\n";
            }
            echo "\n";
        }
        
        // Recommendations
        $this->generateApiRecommendations();
    }
    
    /**
     * Generate API-specific recommendations
     */
    private function generateApiRecommendations()
    {
        echo "💡 RECOMMENDATIONS:\n";
        
        if (isset($this->results['message_sending'])) {
            $msg = $this->results['message_sending'];
            if ($msg['success_rate'] < 0.95) {
                echo "   ⚠️  Low message success rate - check WhatsApp API configuration\n";
            }
        }
        
        if (isset($this->results['bulk_messaging'])) {
            $maxBulk = max(array_keys($this->results['bulk_messaging']));
            $maxRate = max(array_column($this->results['bulk_messaging'], 'messages_per_second'));
            echo "   • Maximum bulk size: {$maxBulk} messages\n";
            echo "   • Maximum sending rate: " . number_format($maxRate, 2) . " messages/second\n";
        }
        
        if (isset($this->results['concurrent_sessions'])) {
            $maxSessions = max(array_keys($this->results['concurrent_sessions']));
            echo "   • Maximum concurrent sessions: {$maxSessions}\n";
        }
        
        echo "\n🔧 OPTIMIZATION SUGGESTIONS:\n";
        echo "   • Implement message queuing for bulk operations\n";
        echo "   • Add retry logic for failed messages\n";
        echo "   • Use webhooks for delivery status updates\n";
        echo "   • Implement rate limiting on your end\n";
        echo "   • Monitor WhatsApp API quotas\n";
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    if (isset($argv[1]) && $argv[1] === '--single-session') {
        $sessionId = isset($argv[3]) ? (int)$argv[3] : 0;
        $tester = new WhatsAppApiTester();
        $result = $tester->simulateSingleSession($sessionId);
        echo json_encode($result);
    } else {
        $tester = new WhatsAppApiTester();
        $tester->runApiTests();
    }
}
