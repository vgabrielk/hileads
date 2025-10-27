<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BestfyService;
use App\Models\Plan;

class TestBestfyConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bestfy:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a conexão com a API da Bestfy';

    /**
     * Execute the console command.
     */
    public function handle(BestfyService $bestfyService)
    {
        $this->info('🔍 Testando conexão com a API da Bestfy...');
        $this->newLine();

        // Verifica configurações
        $this->info('📋 Verificando configurações:');
        $this->line('   Base URL: ' . config('services.bestfy.base_url'));
        $this->line('   Secret Key: ' . (config('services.bestfy.secret_key') ? '✓ Configurada' : '✗ Não configurada'));
        $this->line('   Public Key: ' . (config('services.bestfy.public_key') ? '✓ Configurada' : '✗ Não configurada'));
        $this->newLine();

        // Testa criação de checkout
        $this->info('🔄 Tentando criar um checkout de teste...');
        
        $plan = Plan::first();
        if (!$plan) {
            $this->error('✗ Nenhum plano encontrado no banco de dados.');
            $this->info('Execute: php artisan db:seed --class=PlanSeeder');
            return 1;
        }

        $this->line("   Usando plano: {$plan->name} (R$ {$plan->price})");
        
        try {
            $user = \App\Models\User::first();
            if (!$user) {
                $this->error('✗ Nenhum usuário encontrado no banco de dados.');
                return 1;
            }

            $checkout = $bestfyService->createCheckout($plan, $user, 'https://example.com/webhook');
            
            $this->newLine();
            $this->info('✓ Checkout criado com sucesso!');
            $this->line('   Checkout ID: ' . ($checkout['id'] ?? 'N/A'));
            $this->line('   Secure URL: ' . ($checkout['secureUrl'] ?? 'N/A'));
            $this->newLine();
            
            return 0;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('✗ Erro ao criar checkout:');
            $this->line('   ' . $e->getMessage());
            $this->newLine();
            
            $this->warn('💡 Possíveis causas:');
            $this->line('   1. Chaves da API incorretas ou inválidas');
            $this->line('   2. Ambiente de produção/sandbox incorreto');
            $this->line('   3. IP bloqueado pela API');
            $this->line('   4. Problemas de conectividade');
            $this->newLine();
            
            return 1;
        }
    }
}
