<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminLogsController extends Controller
{
    /**
     * Display the logs page.
     */
    public function index(Request $request)
    {
        $logLevel = $request->get('level', 'all');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 50);

        $logs = $this->getLogs($logLevel, $date, $search, $perPage);
        $logFiles = $this->getAvailableLogFiles();
        $logLevels = ['all', 'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

        return view('admin.logs.index', compact(
            'logs',
            'logFiles',
            'logLevels',
            'logLevel',
            'date',
            'search',
            'perPage'
        ));
    }

    /**
     * Display system logs.
     */
    public function system(Request $request)
    {
        $logLevel = $request->get('level', 'all');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 50);

        $logs = $this->getSystemLogs($logLevel, $date, $search, $perPage);
        $logLevels = ['all', 'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

        return view('admin.logs.system', compact(
            'logs',
            'logLevels',
            'logLevel',
            'date',
            'search',
            'perPage'
        ));
    }

    /**
     * Display user activity logs.
     */
    public function activity(Request $request)
    {
        $userId = $request->get('user_id');
        $action = $request->get('action');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 50);

        $logs = $this->getActivityLogs($userId, $action, $date, $search, $perPage);
        $users = \App\Models\User::orderBy('name')->get();
        $actions = $this->getAvailableActions();

        return view('admin.logs.activity', compact(
            'logs',
            'users',
            'actions',
            'userId',
            'action',
            'date',
            'search',
            'perPage'
        ));
    }

    /**
     * Display error logs.
     */
    public function errors(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 50);

        $logs = $this->getErrorLogs($date, $search, $perPage);

        return view('admin.logs.errors', compact(
            'logs',
            'date',
            'search',
            'perPage'
        ));
    }

    /**
     * Download log file.
     */
    public function download(Request $request)
    {
        $filename = $request->get('file');
        $logPath = storage_path('logs/' . $filename);

        if (!File::exists($logPath)) {
            return redirect()->back()->with('error', 'Arquivo de log não encontrado.');
        }

        return response()->download($logPath);
    }

    /**
     * Show full log details.
     */
    public function show(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $index = $request->get('index', 0);
        $type = $request->get('type', 'laravel');
        
        $log = $this->getLogByIndex($date, $index, $type);
        
        if (!$log) {
            return redirect()->back()->with('error', 'Log não encontrado.');
        }
        
        return view('admin.logs.show', compact('log', 'date', 'index', 'type'));
    }

    /**
     * Clear log files.
     */
    public function clear(Request $request)
    {
        try {
            $type = $request->get('type', 'all');
            
            if ($type === 'all' || $type === 'laravel') {
                $this->clearLaravelLogs();
            }
            
            if ($type === 'all' || $type === 'system') {
                $this->clearSystemLogs();
            }

            Log::info('Logs cleared by admin', [
                'admin_id' => auth()->id(),
                'type' => $type
            ]);

            return redirect()->back()->with('success', 'Logs limpos com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to clear logs', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao limpar logs: ' . $e->getMessage());
        }
    }

    /**
     * Get logs from Laravel log files.
     */
    private function getLogs($level, $date, $search, $perPage)
    {
        $logFile = storage_path('logs/laravel-' . $date . '.log');
        
        if (!File::exists($logFile)) {
            return collect();
        }

        $content = File::get($logFile);
        $lines = explode("\n", $content);
        $logs = collect();

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $logEntry = $this->parseLogLine($line);
            
            if ($logEntry && $this->matchesFilter($logEntry, $level, $search)) {
                $logs->push($logEntry);
            }
        }

        return $logs->reverse()->take($perPage);
    }

    /**
     * Get system logs.
     */
    private function getSystemLogs($level, $date, $search, $perPage)
    {
        // This would typically read from system log files
        // For now, we'll return Laravel logs as system logs
        return $this->getLogs($level, $date, $search, $perPage);
    }

    /**
     * Get activity logs.
     */
    private function getActivityLogs($userId, $action, $date, $search, $perPage)
    {
        // This would typically read from a dedicated activity log table
        // For now, we'll return Laravel logs filtered by user activity
        $logs = $this->getLogs('all', $date, $search, $perPage * 2);
        
        return $logs->filter(function ($log) use ($userId, $action) {
            if ($userId && !str_contains($log['message'], "user_id\":{$userId}")) {
                return false;
            }
            
            if ($action && !str_contains($log['message'], $action)) {
                return false;
            }
            
            return true;
        })->take($perPage);
    }

    /**
     * Get error logs.
     */
    private function getErrorLogs($date, $search, $perPage)
    {
        $logs = $this->getLogs('error', $date, $search, $perPage * 2);
        
        return $logs->filter(function ($log) {
            return in_array($log['level'], ['error', 'critical', 'alert', 'emergency']);
        })->take($perPage);
    }

    /**
     * Parse a log line into structured data.
     */
    private function parseLogLine($line)
    {
        // Laravel log format: [YYYY-MM-DD HH:MM:SS] local.LEVEL: message {"context": "data"}
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+?)(?:\s+(\{.*\}))?$/';
        
        if (preg_match($pattern, $line, $matches)) {
            return [
                'timestamp' => $matches[1],
                'environment' => $matches[2],
                'level' => strtoupper($matches[3]),
                'message' => $matches[4],
                'context' => isset($matches[5]) ? json_decode($matches[5], true) : null,
                'raw' => $line
            ];
        }

        return null;
    }

    /**
     * Check if log entry matches filter criteria.
     */
    private function matchesFilter($logEntry, $level, $search)
    {
        if ($level !== 'all' && strtolower($logEntry['level']) !== $level) {
            return false;
        }

        if ($search && !str_contains(strtolower($logEntry['message']), strtolower($search))) {
            return false;
        }

        return true;
    }

    /**
     * Get available log files.
     */
    private function getAvailableLogFiles()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $logFiles = collect();

        foreach ($files as $file) {
            if (str_ends_with($file->getFilename(), '.log')) {
                $logFiles->push([
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified' => Carbon::createFromTimestamp($file->getMTime())
                ]);
            }
        }

        return $logFiles->sortByDesc('modified');
    }

    /**
     * Get available actions for filtering.
     */
    private function getAvailableActions()
    {
        return [
            'login' => 'Login',
            'logout' => 'Logout',
            'register' => 'Registro',
            'subscription' => 'Subscrição',
            'campaign' => 'Campanha',
            'whatsapp' => 'WhatsApp',
            'settings' => 'Configurações',
            'admin' => 'Administração'
        ];
    }

    /**
     * Clear Laravel logs.
     */
    private function clearLaravelLogs()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        
        foreach ($files as $file) {
            if (str_ends_with($file->getFilename(), '.log')) {
                File::put($file->getPathname(), '');
            }
        }
    }

    /**
     * Clear system logs.
     */
    private function clearSystemLogs()
    {
        // This would clear system logs
        // Implementation depends on the system
    }

    /**
     * Get a specific log entry by index.
     */
    private function getLogByIndex($date, $index, $type)
    {
        $logFile = $this->getLogFilePath($date, $type);
        
        if (!File::exists($logFile)) {
            return null;
        }

        $content = File::get($logFile);
        $lines = explode("\n", $content);
        $logs = collect();

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $logEntry = $this->parseLogLine($line);
            
            if ($logEntry) {
                $logs->push($logEntry);
            }
        }

        $reversedLogs = $logs->reverse();
        
        return $reversedLogs->get($index);
    }

    /**
     * Get log file path based on type and date.
     */
    private function getLogFilePath($date, $type)
    {
        switch ($type) {
            case 'create':
                return storage_path('logs/create-' . $date . '.log');
            case 'laravel':
            default:
                return storage_path('logs/laravel-' . $date . '.log');
        }
    }
}