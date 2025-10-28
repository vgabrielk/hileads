<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class CampaignLogger
{
    private static $logFile = 'create.log';
    
    /**
     * Log específico para campanhas de mídia
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        $logMessage = "[" . now()->format('Y-m-d H:i:s') . "] {$level}: {$message}";
        
        if (!empty($context)) {
            $logMessage .= " " . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        
        $logMessage .= "\n" . str_repeat('-', 80) . "\n";
        
        file_put_contents(
            storage_path('logs/' . self::$logFile),
            $logMessage,
            FILE_APPEND | LOCK_EX
        );
    }
    
    /**
     * Log de início de processo
     */
    public static function startProcess(string $process, array $context = []): void
    {
        self::log('START', "🚀 Iniciando processo: {$process}", $context);
    }
    
    /**
     * Log de fim de processo
     */
    public static function endProcess(string $process, array $context = []): void
    {
        self::log('END', "✅ Finalizando processo: {$process}", $context);
    }
    
    /**
     * Log de erro
     */
    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', "❌ {$message}", $context);
    }
    
    /**
     * Log de warning
     */
    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', "⚠️ {$message}", $context);
    }
    
    /**
     * Log de info
     */
    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', "ℹ️ {$message}", $context);
    }
    
    /**
     * Log de debug
     */
    public static function debug(string $message, array $context = []): void
    {
        self::log('DEBUG', "🔍 {$message}", $context);
    }
    
    /**
     * Log de dados de mídia
     */
    public static function mediaData(string $message, array $mediaData): void
    {
        // Sanitizar dados sensíveis
        $sanitized = $mediaData;
        if (isset($sanitized['base64'])) {
            $base64 = $sanitized['base64'];
            $sanitized['base64'] = [
                'length' => strlen($base64),
                'prefix' => substr($base64, 0, 50) . '...',
                'suffix' => '...' . substr($base64, -20),
                'is_valid_format' => preg_match('/^data:[^;]+;base64,/', $base64),
                'mime_type' => preg_match('/^data:([^;]+);base64,/', $base64, $matches) ? $matches[1] : null
            ];
        }
        
        self::log('MEDIA', "📷 {$message}", $sanitized);
    }
    
    /**
     * Log de validação
     */
    public static function validation(string $message, array $context = []): void
    {
        self::log('VALIDATION', "✅ {$message}", $context);
    }
    
    /**
     * Log de API
     */
    public static function api(string $message, array $context = []): void
    {
        self::log('API', "🌐 {$message}", $context);
    }
    
    /**
     * Log de banco de dados
     */
    public static function database(string $message, array $context = []): void
    {
        self::log('DATABASE', "💾 {$message}", $context);
    }
}
