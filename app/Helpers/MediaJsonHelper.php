<?php

namespace App\Helpers;

class MediaJsonHelper
{
    /**
     * Cria JSON correto para dados de mídia
     */
    public static function createMediaJson(array $mediaData): string
    {
        // Limpar Base64 se presente
        if (isset($mediaData['base64'])) {
            $mediaData['base64'] = self::cleanBase64($mediaData['base64']);
        }
        
        // Criar JSON com flags corretas
        return json_encode($mediaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Limpa Base64 removendo quebras de linha e espaços
     */
    public static function cleanBase64(string $base64): string
    {
        // Remover quebras de linha e espaços
        $cleaned = str_replace(["\r", "\n", " ", "\t"], '', $base64);
        
        // Validar se a Base64 é válida
        if (self::isValidBase64($cleaned)) {
            return $cleaned;
        }
        
        // Se falhou, retornar original
        return $base64;
    }
    
    /**
     * Valida se uma string é Base64 válida
     */
    public static function isValidBase64(string $base64): bool
    {
        // Verificar se é um data URL
        if (preg_match('/^data:([^;]+);base64,(.+)$/', $base64, $matches)) {
            $base64Content = $matches[2];
            return base64_decode($base64Content, true) !== false;
        }
        
        // Verificar se é Base64 puro
        return base64_decode($base64, true) !== false;
    }
    
    /**
     * Cria dados de mídia a partir de arquivo
     */
    public static function createFromFile(string $filePath, string $mimeType = null): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Arquivo não encontrado: {$filePath}");
        }
        
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);
        
        // Detectar MIME type se não fornecido
        if (!$mimeType) {
            $mimeType = mime_content_type($filePath);
        }
        
        // Ler arquivo e converter para Base64
        $fileContent = file_get_contents($filePath);
        $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
        
        return [
            'name' => $fileName,
            'type' => $mimeType,
            'base64' => $base64,
            'size' => $fileSize
        ];
    }
    
    /**
     * Cria dados de mídia a partir de Base64
     */
    public static function createFromBase64(string $base64, string $fileName, string $mimeType, int $size = null): array
    {
        // Limpar Base64
        $cleanedBase64 = self::cleanBase64($base64);
        
        // Validar Base64
        if (!self::isValidBase64($cleanedBase64)) {
            throw new \InvalidArgumentException("Base64 inválida");
        }
        
        return [
            'name' => $fileName,
            'type' => $mimeType,
            'base64' => $cleanedBase64,
            'size' => $size ?? strlen($cleanedBase64)
        ];
    }
}
