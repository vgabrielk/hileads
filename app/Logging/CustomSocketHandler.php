<?php

namespace App\Logging;

use Monolog\Handler\SocketHandler;
use Monolog\LogRecord;

class CustomSocketHandler extends SocketHandler
{
    /**
     * Timeout para conexão em segundos
     */
    protected int $connectionTimeout = 10;
    
    /**
     * Timeout para escrita em segundos
     */
    protected int $writeTimeout = 30;
    
    /**
     * Número máximo de tentativas
     */
    protected int $maxRetries = 3;
    
    /**
     * Delay entre tentativas em milissegundos
     */
    protected int $retryDelay = 1000;

    public function __construct(
        string $connectionString,
        int $level = 100,
        bool $bubble = true,
        ?int $connectionTimeout = null,
        ?int $writeTimeout = null,
        ?int $maxRetries = null,
        ?int $retryDelay = null
    ) {
        parent::__construct($connectionString, $level, $bubble);
        
        $this->connectionTimeout = $connectionTimeout ?? $this->connectionTimeout;
        $this->writeTimeout = $writeTimeout ?? $this->writeTimeout;
        $this->maxRetries = $maxRetries ?? $this->maxRetries;
        $this->retryDelay = $retryDelay ?? $this->retryDelay;
    }

    /**
     * Escreve o log com retry e timeout customizados
     */
    protected function write(LogRecord $record): void
    {
        $attempt = 0;
        $lastException = null;
        
        while ($attempt < $this->maxRetries) {
            try {
                // Configurar timeouts antes de cada tentativa
                $this->setConnectionTimeout($this->connectionTimeout);
                $this->setTimeout($this->writeTimeout);
                
                parent::write($record);
                return; // Sucesso, sair do loop
                
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;
                
                // Log do erro (sem usar o próprio sistema de log para evitar loops)
                error_log(sprintf(
                    'CustomSocketHandler: Tentativa %d/%d falhou: %s',
                    $attempt,
                    $this->maxRetries,
                    $e->getMessage()
                ));
                
                // Se não é a última tentativa, aguardar antes de tentar novamente
                if ($attempt < $this->maxRetries) {
                    usleep($this->retryDelay * 1000); // Converter para microssegundos
                }
            }
        }
        
        // Se todas as tentativas falharam, logar o erro final
        error_log(sprintf(
            'CustomSocketHandler: Todas as %d tentativas falharam. Último erro: %s',
            $this->maxRetries,
            $lastException ? $lastException->getMessage() : 'Erro desconhecido'
        ));
        
        // Não relançar a exceção para evitar quebrar a aplicação
        // O log será perdido, mas a aplicação continuará funcionando
    }

    /**
     * Configura o timeout de conexão
     */
    protected function setConnectionTimeout(int $timeout): void
    {
        if (function_exists('stream_set_timeout')) {
            $this->connectionTimeout = $timeout;
        }
    }

    /**
     * Configura o timeout de escrita
     */
    protected function setTimeout(int $timeout): void
    {
        if (function_exists('stream_set_timeout')) {
            $this->writeTimeout = $timeout;
        }
    }

    /**
     * Verifica se a conexão está ativa
     */
    protected function isConnectionActive(): bool
    {
        return is_resource($this->resource) && !feof($this->resource);
    }

    /**
     * Reconecta se necessário
     */
    protected function reconnectIfNeeded(): void
    {
        if (!$this->isConnectionActive()) {
            $this->closeSocket();
            $this->connect();
        }
    }
}
