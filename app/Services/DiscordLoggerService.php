<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordLoggerService
{
    private string $webhookUrl;
    
    public function __construct()
    {
        $this->webhookUrl = 'https://discord.com/api/webhooks/1432468525787910256/ps84js_j2OacGgrzPdZbQplMkSvQNLL2s8vCD3ULrq5snTdKtkCFC483OqPW5u1QQJxY';
    }
    
    /**
     * Envia uma mensagem de erro para o Discord
     */
    public function logError(string $title, string $message, array $context = []): void
    {
        try {
            // Verificar se a URL do webhook está configurada
            if (empty($this->webhookUrl)) {
                \Log::warning('Discord Webhook URL não configurada. Pulando envio para Discord.');
                return;
            }
            
            $embed = [
                'title' => $title,
                'description' => $message,
                'color' => 15158332, // Vermelho
                'timestamp' => now()->toISOString(),
                'fields' => []
            ];
            
            // Adicionar campos do contexto (limitando tamanho)
            foreach ($context as $key => $value) {
                $fieldValue = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : (string)$value;
                
                // Limitar tamanho do campo (Discord tem limite de 1024 caracteres)
                if (strlen($fieldValue) > 1000) {
                    $fieldValue = substr($fieldValue, 0, 997) . '...';
                }
                
                $embed['fields'][] = [
                    'name' => $key,
                    'value' => $fieldValue,
                    'inline' => false
                ];
            }
            
            $payload = [
                'username' => 'Spidey Bot',
                'embeds' => [$embed]
            ];
            
            // Usar timeout menor e retry
            Http::timeout(5)
                ->retry(2, 1000)
                ->post($this->webhookUrl, $payload);
            
        } catch (\Exception $e) {
            // Se falhar ao enviar para Discord, logar normalmente sem usar Log::error para evitar loop
            error_log('Falha ao enviar log para Discord: ' . $e->getMessage());
        }
    }
    
    /**
     * Envia uma mensagem de sucesso para o Discord
     */
    public function logSuccess(string $title, string $message, array $context = []): void
    {
        try {
            // Verificar se a URL do webhook está configurada
            if (empty($this->webhookUrl)) {
                \Log::warning('Discord Webhook URL não configurada. Pulando envio para Discord.');
                return;
            }
            
            $embed = [
                'title' => $title,
                'description' => $message,
                'color' => 3066993, // Verde
                'timestamp' => now()->toISOString(),
                'fields' => []
            ];
            
            // Adicionar campos do contexto
            foreach ($context as $key => $value) {
                $embed['fields'][] = [
                    'name' => $key,
                    'value' => is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : (string)$value,
                    'inline' => false
                ];
            }
            
            $payload = [
                'username' => 'Spidey Bot',
                'embeds' => [$embed]
            ];
            
            // Usar timeout menor e retry
            Http::timeout(5)
                ->retry(2, 1000)
                ->post($this->webhookUrl, $payload);
            
        } catch (\Exception $e) {
            // Se falhar ao enviar para Discord, logar normalmente sem usar Log::error para evitar loop
            error_log('Falha ao enviar log para Discord: ' . $e->getMessage());
        }
    }
    
    /**
     * Envia uma mensagem de warning para o Discord
     */
    public function logWarning(string $title, string $message, array $context = []): void
    {
        try {
            // Verificar se a URL do webhook está configurada
            if (empty($this->webhookUrl)) {
                \Log::warning('Discord Webhook URL não configurada. Pulando envio para Discord.');
                return;
            }
            
            $embed = [
                'title' => $title,
                'description' => $message,
                'color' => 16776960, // Amarelo
                'timestamp' => now()->toISOString(),
                'fields' => []
            ];
            
            // Adicionar campos do contexto
            foreach ($context as $key => $value) {
                $embed['fields'][] = [
                    'name' => $key,
                    'value' => is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : (string)$value,
                    'inline' => false
                ];
            }
            
            $payload = [
                'username' => 'Spidey Bot',
                'embeds' => [$embed]
            ];
            
            // Usar timeout menor e retry
            Http::timeout(5)
                ->retry(2, 1000)
                ->post($this->webhookUrl, $payload);
            
        } catch (\Exception $e) {
            // Se falhar ao enviar para Discord, logar normalmente sem usar Log::error para evitar loop
            error_log('Falha ao enviar log para Discord: ' . $e->getMessage());
        }
    }
}
