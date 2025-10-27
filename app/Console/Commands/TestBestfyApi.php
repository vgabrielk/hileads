<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BestfyService;
use App\Models\Plan;
use App\Models\User;

class TestBestfyApi extends Command
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
    protected $description = 'Test Bestfy API connection and create a test checkout';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testando integração com Bestfy API...');
        $this->newLine();

        // Verificar configuração
        $this->info('📋 Verificando configuração:');
        $secretKey = config('services.bestfy.secret_key');
        $publicKey = config('services.bestfy.public_key');
        $baseUrl = config('services.bestfy.base_url');

        if (empty($secretKey)) {
            $this->error('❌ BESTFY_SECRET_KEY não configurada no .env');
            return 1;
        }

        if (empty($publicKey)) {
            $this->error('❌ BESTFY_PUBLIC_KEY não configurada no .env');
            return 1;
        }

        $this->info("✅ Base URL: {$baseUrl}");
        $this->info("✅ Secret Key: " . substr($secretKey, 0, 10) . '...');
        $this->info("✅ Public Key: " . substr($publicKey, 0, 10) . '...');
        $this->newLine();

        // Testar conexão
        $this->info('🔗 Testando conexão com a API...');
        $bestfyService = app(BestfyService::class);
        
        try {
            $connectionTest = $bestfyService->testConnection();
            
            if ($connectionTest['success']) {
                $this->info("✅ Conexão OK - Status: {$connectionTest['status']}");
            } else {
                $this->error("❌ Falha na conexão: {$connectionTest['error']}");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Erro na conexão: {$e->getMessage()}");
            return 1;
        }

        $this->newLine();

        // Testar criação de checkout
        $this->info('🛒 Testando criação de checkout...');
        
        try {
            $plan = Plan::first();
            $user = User::first();

            if (!$plan) {
                $this->error('❌ Nenhum plano encontrado. Execute: php artisan db:seed --class=PlanSeeder');
                return 1;
            }

            if (!$user) {
                $this->error('❌ Nenhum usuário encontrado. Crie um usuário primeiro.');
                return 1;
            }

            $this->info("📦 Plano: {$plan->name} (R$ {$plan->price})");
            $this->info("👤 Usuário: {$user->name} ({$user->email})");

            $checkoutData = $bestfyService->createCheckout($plan, $user, 'http://localhost:8000/bestfy/webhook');
            
            $this->info('✅ Checkout criado com sucesso!');
            $this->info("🆔 Checkout ID: {$checkoutData['id']}");
            $this->info("🔗 Secure URL: {$checkoutData['secureUrl']}");
            
            $this->newLine();
            $this->info('🎉 Integração funcionando perfeitamente!');
            $this->info('💡 Para testar o redirecionamento, acesse: ' . route('plans.show', $plan));
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao criar checkout: {$e->getMessage()}");
            
            if (str_contains($e->getMessage(), '401')) {
                $this->newLine();
                $this->warn('🔑 Problema de autenticação detectado!');
                $this->info('📋 Possíveis soluções:');
                $this->info('1. Verifique se as chaves estão corretas no .env');
                $this->info('2. Acesse https://dashboard.bestfybr.com.br');
                $this->info('3. Vá em Configurações → API Keys');
                $this->info('4. Resetar/gerar nova chave');
                $this->info('5. Atualize o .env com a nova chave');
                $this->info('6. Execute: php artisan config:clear');
            }
            
            return 1;
        }

        return 0;
    }
}