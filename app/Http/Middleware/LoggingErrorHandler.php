<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RobustLoggerService;
use Symfony\Component\HttpFoundation\Response;

class LoggingErrorHandler
{
    private RobustLoggerService $robustLogger;
    
    public function __construct()
    {
        $this->robustLogger = new RobustLoggerService();
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Configurar handler de erro personalizado para capturar erros de logging
        set_error_handler(function ($severity, $message, $file, $line) {
            // Verificar se é um erro relacionado ao Monolog/SocketHandler
            if (strpos($message, 'Write timed-out') !== false || 
                strpos($message, 'SocketHandler') !== false ||
                strpos($message, 'Connection timed out') !== false) {
                
                // Usar robust logger para registrar o erro sem causar loops
                $this->robustLogger->error('Erro de timeout no sistema de logging', [
                    'severity' => $severity,
                    'message' => $message,
                    'file' => $file,
                    'line' => $line,
                    'url' => request()->url(),
                    'method' => request()->method()
                ]);
                
                // Não executar o handler de erro padrão para evitar loops
                return true;
            }
            
            // Para outros erros, usar o handler padrão
            return false;
        });
        
        try {
            $response = $next($request);
            
            // Restaurar handler de erro original
            restore_error_handler();
            
            return $response;
            
        } catch (\Exception $e) {
            // Restaurar handler de erro original
            restore_error_handler();
            
            // Se for um erro de logging, tentar continuar sem quebrar a aplicação
            if (strpos($e->getMessage(), 'Write timed-out') !== false ||
                strpos($e->getMessage(), 'SocketHandler') !== false) {
                
                $this->robustLogger->error('Erro de timeout capturado no middleware', [
                    'error' => $e->getMessage(),
                    'url' => $request->url(),
                    'method' => $request->method()
                ]);
                
                // Retornar uma resposta de erro genérica em vez de quebrar
                return response()->json([
                    'error' => 'Erro temporário no sistema de logging. Tente novamente.',
                    'timestamp' => now()->toISOString()
                ], 500);
            }
            
            // Para outros erros, relançar a exceção
            throw $e;
        }
    }
}
