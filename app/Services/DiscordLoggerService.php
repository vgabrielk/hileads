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
            $embed = [
                'title' => $title,
                'description' => $message,
                'color' => 15158332, // Vermelho
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
            
            Http::timeout(10)->post($this->webhookUrl, $payload);
            
        } catch (\Exception $e) {
            // Se falhar ao enviar para Discord, logar normalmente
            Log::error('Falha ao enviar log para Discord: ' . $e->getMessage());
        }
    }
    
    /**
     * Envia uma mensagem de sucesso para o Discord
     */
    public function logSuccess(string $title, string $message, array $context = []): void
    {
        try {
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
            
            Http::timeout(10)->post($this->webhookUrl, $payload);
            
        } catch (\Exception $e) {
            Log::error('Falha ao enviar log para Discord: ' . $e->getMessage());
        }
    }
    
    /**
     * Envia uma mensagem de warning para o Discord
     */
    public function logWarning(string $title, string $message, array $context = []): void
    {
        try {
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
            
            Http::timeout(10)->post($this->webhookUrl, $payload);
            
        } catch (\Exception $e) {
            Log::error('Falha ao enviar log para Discord: ' . $e->getMessage());
        }
    }
}
