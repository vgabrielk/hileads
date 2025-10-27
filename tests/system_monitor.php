<?php

/**
 * System Resource Monitor
 * 
 * Monitors system resources during load testing to identify bottlenecks
 */

class SystemMonitor
{
    private $logFile;
    private $startTime;
    private $isRunning = false;
    
    public function __construct($logFile = '/tmp/system_monitor.log')
    {
        $this->logFile = $logFile;
        $this->startTime = microtime(true);
    }
    
    /**
     * Start monitoring system resources
     */
    public function startMonitoring($interval = 1)
    {
        $this->isRunning = true;
        echo "🔍 Starting system monitoring...\n";
        echo "📝 Logging to: {$this->logFile}\n";
        echo "⏱️  Monitoring interval: {$interval}s\n\n";
        
        // Clear previous log
        file_put_contents($this->logFile, "timestamp,memory_usage,memory_peak,cpu_usage,disk_usage,db_connections,active_users\n");
        
        while ($this->isRunning) {
            $metrics = $this->collectMetrics();
            $this->logMetrics($metrics);
            
            // Display current status
            echo sprintf("\r💾 Memory: %s | 🖥️  CPU: %s%% | 💽 Disk: %s | 👥 Users: %s",
                $this->formatBytes($metrics['memory_usage']),
                $metrics['cpu_usage'],
                $this->formatBytes($metrics['disk_usage']),
                $metrics['active_users']
            );
            
            sleep($interval);
        }
    }
    
    /**
     * Stop monitoring
     */
    public function stopMonitoring()
    {
        $this->isRunning = false;
        echo "\n\n🛑 System monitoring stopped.\n";
        echo "📊 Log file: {$this->logFile}\n";
    }
    
    /**
     * Collect current system metrics
     */
    private function collectMetrics()
    {
        return [
            'timestamp' => time(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'cpu_usage' => $this->getCpuUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'db_connections' => $this->getDatabaseConnections(),
            'active_users' => $this->getActiveUsers()
        ];
    }
    
    /**
     * Get CPU usage percentage
     */
    private function getCpuUsage()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return round($load[0] * 100, 2);
        }
        
        // Fallback for systems without sys_getloadavg
        $stat1 = $this->getCpuStats();
        sleep(1);
        $stat2 = $this->getCpuStats();
        
        $idle1 = $stat1['idle'];
        $idle2 = $stat2['idle'];
        $total1 = $stat1['total'];
        $total2 = $stat2['total'];
        
        $idle = $idle2 - $idle1;
        $total = $total2 - $total1;
        
        return $total > 0 ? round((1 - $idle / $total) * 100, 2) : 0;
    }
    
    /**
     * Get CPU statistics
     */
    private function getCpuStats()
    {
        $stat = file_get_contents('/proc/stat');
        $lines = explode("\n", $stat);
        $cpu = explode(' ', $lines[0]);
        
        return [
            'user' => $cpu[1],
            'nice' => $cpu[2],
            'system' => $cpu[3],
            'idle' => $cpu[4],
            'iowait' => $cpu[5],
            'irq' => $cpu[6],
            'softirq' => $cpu[7],
            'total' => array_sum(array_slice($cpu, 1, 7))
        ];
    }
    
    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $bytes = disk_free_space('/');
        return $bytes !== false ? $bytes : 0;
    }
    
    /**
     * Get database connections count
     */
    private function getDatabaseConnections()
    {
        try {
            // Try to connect to database and get connection count
            $pdo = new PDO(
                'mysql:host=localhost;dbname=hileads',
                'root',
                ''
            );
            
            $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['Value'] : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get active users count (approximate)
     */
    private function getActiveUsers()
    {
        // Count active sessions in the last 5 minutes
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=hileads',
                'root',
                ''
            );
            
            $stmt = $pdo->query("
                SELECT COUNT(DISTINCT user_id) as active_users 
                FROM sessions 
                WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['active_users'] : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Log metrics to file
     */
    private function logMetrics($metrics)
    {
        $line = implode(',', $metrics) . "\n";
        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
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
    
    /**
     * Generate performance report
     */
    public function generateReport()
    {
        if (!file_exists($this->logFile)) {
            echo "❌ No monitoring data found.\n";
            return;
        }
        
        $data = [];
        $handle = fopen($this->logFile, 'r');
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = array_combine($header, $row);
        }
        fclose($handle);
        
        if (empty($data)) {
            echo "❌ No data to analyze.\n";
            return;
        }
        
        echo "\n📊 SYSTEM PERFORMANCE REPORT\n";
        echo str_repeat("=", 60) . "\n\n";
        
        // Memory analysis
        $memoryUsage = array_column($data, 'memory_usage');
        $memoryPeak = array_column($data, 'memory_peak');
        
        echo "💾 MEMORY USAGE:\n";
        echo "   • Average: " . $this->formatBytes(array_sum($memoryUsage) / count($memoryUsage)) . "\n";
        echo "   • Peak: " . $this->formatBytes(max($memoryPeak)) . "\n";
        echo "   • Min: " . $this->formatBytes(min($memoryUsage)) . "\n\n";
        
        // CPU analysis
        $cpuUsage = array_column($data, 'cpu_usage');
        echo "🖥️  CPU USAGE:\n";
        echo "   • Average: " . number_format(array_sum($cpuUsage) / count($cpuUsage), 2) . "%\n";
        echo "   • Peak: " . number_format(max($cpuUsage), 2) . "%\n";
        echo "   • Min: " . number_format(min($cpuUsage), 2) . "%\n\n";
        
        // Database connections
        $dbConnections = array_column($data, 'db_connections');
        echo "🗄️  DATABASE CONNECTIONS:\n";
        echo "   • Average: " . number_format(array_sum($dbConnections) / count($dbConnections), 2) . "\n";
        echo "   • Peak: " . max($dbConnections) . "\n";
        echo "   • Min: " . min($dbConnections) . "\n\n";
        
        // Active users
        $activeUsers = array_column($data, 'active_users');
        echo "👥 ACTIVE USERS:\n";
        echo "   • Average: " . number_format(array_sum($activeUsers) / count($activeUsers), 2) . "\n";
        echo "   • Peak: " . max($activeUsers) . "\n";
        echo "   • Min: " . min($activeUsers) . "\n\n";
        
        // Recommendations
        $this->generateRecommendations($data);
    }
    
    /**
     * Generate performance recommendations
     */
    private function generateRecommendations($data)
    {
        echo "🔍 RECOMMENDATIONS:\n";
        
        $avgMemory = array_sum(array_column($data, 'memory_usage')) / count($data);
        $peakMemory = max(array_column($data, 'memory_peak'));
        $avgCpu = array_sum(array_column($data, 'cpu_usage')) / count($data);
        $peakCpu = max(array_column($data, 'cpu_usage'));
        $peakConnections = max(array_column($data, 'db_connections'));
        
        if ($peakMemory > 512 * 1024 * 1024) { // 512MB
            echo "   ⚠️  High memory usage detected - consider optimizing queries or adding caching\n";
        }
        
        if ($peakCpu > 80) {
            echo "   ⚠️  High CPU usage detected - consider optimizing code or adding more CPU cores\n";
        }
        
        if ($peakConnections > 50) {
            echo "   ⚠️  High database connections - consider connection pooling\n";
        }
        
        if ($avgMemory < 64 * 1024 * 1024) { // 64MB
            echo "   ✅ Memory usage is within acceptable limits\n";
        }
        
        if ($avgCpu < 50) {
            echo "   ✅ CPU usage is within acceptable limits\n";
        }
        
        echo "\n💡 OPTIMIZATION SUGGESTIONS:\n";
        echo "   • Enable OPcache for PHP\n";
        echo "   • Use Redis for session storage\n";
        echo "   • Implement database query caching\n";
        echo "   • Consider using a CDN for static assets\n";
        echo "   • Monitor database slow query log\n";
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    $monitor = new SystemMonitor();
    
    if (isset($argv[1]) && $argv[1] === '--report') {
        $monitor->generateReport();
    } else {
        // Start monitoring
        $interval = isset($argv[1]) ? (int)$argv[1] : 1;
        
        // Handle Ctrl+C gracefully
        pcntl_signal(SIGINT, function() use ($monitor) {
            $monitor->stopMonitoring();
            exit(0);
        });
        
        $monitor->startMonitoring($interval);
    }
}
