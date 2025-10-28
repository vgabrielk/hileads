<?php

namespace App\Helpers;

class JsonSanitizer
{
    /**
     * Sanitiza e decodifica JSON com tratamento de erros
     */
    public static function decode(string $json, bool $assoc = true): ?array
    {
        // Primeira tentativa: decodificar normalmente
        $decoded = json_decode($json, $assoc);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        $originalError = json_last_error();
        
        // Segunda tentativa: limpar Base64 (quebras de linha e espaços)
        $cleaned = self::cleanBase64InJson($json);
        $decoded = json_decode($cleaned, $assoc);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Terceira tentativa: remover caracteres de controle
        $cleaned = preg_replace('/[\x00-\x1F\x7F]/', '', $json);
        $decoded = json_decode($cleaned, $assoc);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Quarta tentativa: remover caracteres de controle mais agressivamente
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $json);
        $decoded = json_decode($cleaned, $assoc);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Quinta tentativa: escapar caracteres problemáticos
        $cleaned = addslashes($json);
        $decoded = json_decode($cleaned, $assoc);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Sexta tentativa: limpar caracteres de controle mais agressivamente
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F\x80-\x9F]/', '', $json);
        $decoded = json_decode($cleaned, $assoc);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Sétima tentativa: reconstruir JSON manualmente
        try {
            $decoded = self::reconstructJson($json);
            if ($decoded !== null) {
                return $decoded;
            }
        } catch (Exception $e) {
            // Ignorar erro e continuar
        }
        
        // Oitava tentativa: usar regex para extrair dados mesmo com JSON quebrado
        try {
            $decoded = self::extractDataWithRegex($json);
            if ($decoded !== null) {
                return $decoded;
            }
        } catch (Exception $e) {
            // Ignorar erro e continuar
        }
        
        // Se todas as tentativas falharam, retornar null
        return null;
    }
    
    /**
     * Limpa Base64 dentro do JSON (remove quebras de linha e espaços)
     */
    private static function cleanBase64InJson(string $json): string
    {
        // Encontrar e limpar Base64 dentro do JSON
        return preg_replace_callback(
            '/"base64"\s*:\s*"([^"]+)"/',
            function ($matches) {
                $base64 = $matches[1];
                
                // Limpar quebras de linha e espaços
                $cleanedBase64 = str_replace(["\r", "\n", " ", "\t"], '', $base64);
                
                // Validar se a Base64 é válida
                if (base64_decode($cleanedBase64, true) === false) {
                    // Se falhou, tentar com a Base64 original
                    return $matches[0];
                }
                
                return '"base64":"' . $cleanedBase64 . '"';
            },
            $json
        );
    }
    
    /**
     * Reconstrói JSON manualmente extraindo dados conhecidos
     */
    private static function reconstructJson(string $json): ?array
    {
        // Extrair name
        preg_match('/"name"\s*:\s*"([^"]+)"/', $json, $nameMatches);
        $name = $nameMatches[1] ?? null;
        
        // Extrair type
        preg_match('/"type"\s*:\s*"([^"]+)"/', $json, $typeMatches);
        $type = $typeMatches[1] ?? null;
        
        // Extrair base64 (mais complexo devido ao tamanho)
        preg_match('/"base64"\s*:\s*"([^"]+)"/', $json, $base64Matches);
        $base64 = $base64Matches[1] ?? null;
        
        // Extrair size se existir
        preg_match('/"size"\s*:\s*(\d+)/', $json, $sizeMatches);
        $size = isset($sizeMatches[1]) ? (int)$sizeMatches[1] : null;
        
        // Se temos pelo menos name, type e base64, reconstruir
        if ($name && $type && $base64) {
            $result = [
                'name' => $name,
                'type' => $type,
                'base64' => $base64
            ];
            
            if ($size !== null) {
                $result['size'] = $size;
            }
            
            return $result;
        }
        
        return null;
    }
    
    /**
     * Extrai dados usando regex mesmo com JSON quebrado
     */
    private static function extractDataWithRegex(string $json): ?array
    {
        $result = [];
        
        // Extrair name
        if (preg_match('/"name"\s*:\s*"([^"]+)"/', $json, $matches)) {
            $result['name'] = $matches[1];
        }
        
        // Extrair type
        if (preg_match('/"type"\s*:\s*"([^"]+)"/', $json, $matches)) {
            $result['type'] = $matches[1];
        }
        
        // Extrair base64 (mais complexo devido ao tamanho)
        if (preg_match('/"base64"\s*:\s*"([^"]+)"/', $json, $matches)) {
            $base64 = $matches[1];
            
            // Limpar quebras de linha e espaços
            $cleanedBase64 = str_replace(["\r", "\n", " ", "\t"], '', $base64);
            
            // Validar Base64
            if (self::isValidBase64($cleanedBase64)) {
                $result['base64'] = $cleanedBase64;
            } else {
                // Tentar com a Base64 original
                if (self::isValidBase64($base64)) {
                    $result['base64'] = $base64;
                }
            }
        }
        
        // Extrair size se existir
        if (preg_match('/"size"\s*:\s*(\d+)/', $json, $matches)) {
            $result['size'] = (int)$matches[1];
        }
        
        // Se temos pelo menos name, type e base64, retornar
        if (isset($result['name']) && isset($result['type']) && isset($result['base64'])) {
            return $result;
        }
        
        return null;
    }
    
    /**
     * Valida se uma string é Base64 válida
     */
    private static function isValidBase64(string $base64): bool
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
     * Verifica se um JSON é válido
     */
    public static function isValid(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Obtém informações detalhadas sobre erro de JSON
     */
    public static function getErrorInfo(string $json): array
    {
        json_decode($json);
        
        return [
            'error_code' => json_last_error(),
            'error_message' => json_last_error_msg(),
            'json_length' => strlen($json),
            'json_preview' => substr($json, 0, 200),
            'has_control_chars' => preg_match('/[\x00-\x1F\x7F]/', $json),
            'control_chars_count' => preg_match_all('/[\x00-\x1F\x7F]/', $json)
        ];
    }
    
    /**
     * Limpa caracteres de controle de uma string
     */
    public static function cleanControlChars(string $string): string
    {
        return preg_replace('/[\x00-\x1F\x7F]/', '', $string);
    }
}
