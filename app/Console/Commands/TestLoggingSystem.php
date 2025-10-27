<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RobustLoggerService;
use Illuminate\Support\Facades\Log;

class TestLoggingSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logging:test {--level=info : Nível de log para testar} {--count=5 : Número de logs para enviar}';

    /**
     * The console command description.
     */
    protected $description = 'Testa o sistema de logging com diferentes níveis e canais';

    private RobustLoggerService $robustLogger;

    public function __construct()
    {
        parent::__construct();
        $this->robustLogger = new RobustLoggerService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $level = $this->option('level');
        $count = (int) $this->option('count');
        
        $this->info("🧪 Testando sistema de logging...");
        $this->info("Nível: {$level}");
        $this->info("Quantidade: {$count}");
        $this->newLine();
        
        // Testar robust logger
        $this->info("📊 Testando RobustLoggerService...");
        $this->testRobustLogger($level, $count);
        
        $this->newLine();
        
        // Testar canais individuais
        $this->info("🔍 Testando canais individuais...");
        $this->testIndividualChannels($level);
        
        $this->newLine();
        
        // Mostrar estatísticas
        $this->info("📈 Estatísticas de falhas:");
        $this->showFailureStats();
        
        $this->newLine();
        $this->info("✅ Teste concluído!");
    }
    
    private function testRobustLogger(string $level, int $count): void
    {
        $startTime = microtime(true);
        $successCount = 0;
        $errorCount = 0;
        
        for ($i = 1; $i <= $count; $i++) {
            try {
                $message = "Teste de logging #{$i} - " . now()->toDateTimeString();
                $context = [
                    'test_id' => $i,
                    'timestamp' => now()->toISOString(),
                    'memory_usage' => memory_get_usage(true),
                    'memory_peak' => memory_get_peak_usage(true)
                ];
                
                $this->robustLogger->{$level}($message, $context);
                $successCount++;
                
                $this->line("  ✅ Log #{$i} enviado com sucesso");
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ❌ Erro no log #{$i}: " . $e->getMessage());
            }
            
            // Pequena pausa para não sobrecarregar
            usleep(100000); // 0.1 segundo
        }
        
        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);
        
        $this->info("  📊 Resultados:");
        $this->line("    • Sucessos: {$successCount}");
        $this->line("    • Erros: {$errorCount}");
        $this->line("    • Tempo total: {$duration}s");
        $this->line("    • Tempo médio por log: " . round($duration / $count, 3) . "s");
    }
    
    private function testIndividualChannels(string $level): void
    {
        $channels = ['single', 'daily', 'robust', 'fallback'];
        
        foreach ($channels as $channel) {
            try {
                $this->line("  🔍 Testando canal: {$channel}");
                
                $logger = Log::channel($channel);
                $message = "Teste individual do canal {$channel} - " . now()->toDateTimeString();
                $context = ['channel' => $channel, 'test_time' => now()->toISOString()];
                
                $logger->{$level}($message, $context);
                $this->line("    ✅ Canal {$channel} funcionando");
                
            } catch (\Exception $e) {
                $this->error("    ❌ Canal {$channel} falhou: " . $e->getMessage());
            }
        }
    }
    
    private function showFailureStats(): void
    {
        $stats = $this->robustLogger->getFailureStats();
        
        if (empty($stats)) {
            $this->line("  🎉 Nenhuma falha registrada!");
            return;
        }
        
        foreach ($stats as $channel => $stat) {
            $status = $stat['is_recent'] ? '🔴' : '🟡';
            $this->line("  {$status} Canal {$channel}:");
            $this->line("    • Falhou em: {$stat['failed_at']}");
            $this->line("    • Tempo desde falha: {$stat['time_since_failure']}s");
        }
    }
}
