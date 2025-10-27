<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class RobustLoggerService
{
    /**
     * Canais de logging em ordem de prioridade
     */
    private array $channels = ['robust', 'fallback'];
    
    /**
     * Cache de canais que falharam para evitar tentativas desnecessárias
     */
    private array $failedChannels = [];
    
    /**
     * Tempo de cache para canais falhados (em segundos)
     */
    private int $failureCacheTime = 300; // 5 minutos
    
    /**
     * Log de erro com fallback automático
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }
    
    /**
     * Log de warning com fallback automático
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }
    
    /**
     * Log de info com fallback automático
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }
    
    /**
     * Log de debug com fallback automático
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
    
    /**
     * Log de critical com fallback automático
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }
    
    /**
     * Método principal de logging com fallback
     */
    private function log(string $level, string $message, array $context = []): void
    {
        $success = false;
        $lastException = null;
        
        foreach ($this->channels as $channel) {
            // Pular canais que falharam recentemente
            if ($this->isChannelRecentlyFailed($channel)) {
                continue;
            }
            
            try {
                $this->writeToChannel($channel, $level, $message, $context);
                $success = true;
                
                // Se o canal funcionou, remover da lista de falhas
                $this->removeChannelFromFailures($channel);
                break;
                
            } catch (\Exception $e) {
                $lastException = $e;
                $this->markChannelAsFailed($channel);
                
                // Log da falha sem usar o sistema de logging para evitar loops
                error_log(sprintf(
                    'RobustLoggerService: Canal %s falhou: %s',
                    $channel,
                    $e->getMessage()
                ));
            }
        }
        
        // Se todos os canais falharam, usar error_log como último recurso
        if (!$success) {
            $this->emergencyLog($level, $message, $context, $lastException);
        }
    }
    
    /**
     * Escreve no canal específico
     */
    private function writeToChannel(string $channel, string $level, string $message, array $context): void
    {
        $logger = Log::channel($channel);
        
        // Usar timeout menor para evitar travamentos
        $originalTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 5);
        
        try {
            $logger->{$level}($message, $context);
        } finally {
            // Restaurar timeout original
            ini_set('default_socket_timeout', $originalTimeout);
        }
    }
    
    /**
     * Verifica se o canal falhou recentemente
     */
    private function isChannelRecentlyFailed(string $channel): bool
    {
        if (!isset($this->failedChannels[$channel])) {
            return false;
        }
        
        $failureTime = $this->failedChannels[$channel];
        return (time() - $failureTime) < $this->failureCacheTime;
    }
    
    /**
     * Marca canal como falhado
     */
    private function markChannelAsFailed(string $channel): void
    {
        $this->failedChannels[$channel] = time();
    }
    
    /**
     * Remove canal da lista de falhas
     */
    private function removeChannelFromFailures(string $channel): void
    {
        unset($this->failedChannels[$channel]);
    }
    
    /**
     * Log de emergência usando error_log
     */
    private function emergencyLog(string $level, string $message, array $context, ?\Exception $lastException = null): void
    {
        $logMessage = sprintf(
            '[%s] %s: %s | Context: %s',
            strtoupper($level),
            now()->toDateTimeString(),
            $message,
            json_encode($context, JSON_UNESCAPED_UNICODE)
        );
        
        if ($lastException) {
            $logMessage .= sprintf(' | Last Error: %s', $lastException->getMessage());
        }
        
        error_log($logMessage);
        
        // Também tentar escrever em arquivo de emergência
        $this->writeToEmergencyFile($logMessage);
    }
    
    /**
     * Escreve em arquivo de emergência
     */
    private function writeToEmergencyFile(string $message): void
    {
        try {
            $emergencyFile = storage_path('logs/emergency.log');
            $logEntry = $message . PHP_EOL;
            
            File::append($emergencyFile, $logEntry);
        } catch (\Exception $e) {
            // Se até o arquivo de emergência falhar, não há muito o que fazer
            error_log('Falha crítica no sistema de logging: ' . $e->getMessage());
        }
    }
    
    /**
     * Limpa cache de canais falhados
     */
    public function clearFailureCache(): void
    {
        $this->failedChannels = [];
    }
    
    /**
     * Obtém estatísticas de falhas
     */
    public function getFailureStats(): array
    {
        $stats = [];
        $currentTime = time();
        
        foreach ($this->failedChannels as $channel => $failureTime) {
            $stats[$channel] = [
                'failed_at' => date('Y-m-d H:i:s', $failureTime),
                'time_since_failure' => $currentTime - $failureTime,
                'is_recent' => $this->isChannelRecentlyFailed($channel)
            ];
        }
        
        return $stats;
    }
}
