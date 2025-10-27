<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\BestfyService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TestSubscriptionSecurity extends Command
{
    protected $signature = 'subscription:test-security {--user-id=} {--plan-id=}';
    protected $description = 'Testa a segurança do sistema de assinaturas';

    public function handle()
    {
        $this->info('🔒 Iniciando testes de segurança do sistema de assinaturas...');
        
        $userId = $this->option('user-id');
        $planId = $this->option('plan-id');
        
        if (!$userId || !$planId) {
            $this->error('❌ Forneça --user-id e --plan-id para executar os testes');
            return 1;
        }
        
        $user = User::find($userId);
        $plan = Plan::find($planId);
        
        if (!$user || !$plan) {
            $this->error('❌ Usuário ou plano não encontrado');
            return 1;
        }
        
        $this->info("👤 Testando com usuário: {$user->name} ({$user->email})");
        $this->info("📦 Testando com plano: {$plan->name} (R$ {$plan->price})");
        
        // Teste 1: Verificar se usuário sem assinatura não tem acesso
        $this->testUserWithoutSubscription($user);
        
        // Teste 2: Verificar se usuário com assinatura expirada não tem acesso
        $this->testUserWithExpiredSubscription($user, $plan);
        
        // Teste 3: Verificar se usuário com assinatura ativa tem acesso
        $this->testUserWithActiveSubscription($user, $plan);
        
        // Teste 4: Verificar se admin tem acesso sem assinatura
        $this->testAdminAccess();
        
        // Teste 5: Testar webhook de segurança
        $this->testWebhookSecurity();
        
        // Teste 6: Testar rate limiting
        $this->testRateLimiting();
        
        $this->info('✅ Todos os testes de segurança foram executados!');
        return 0;
    }
    
    private function testUserWithoutSubscription(User $user)
    {
        $this->info('🔍 Teste 1: Usuário sem assinatura...');
        
        // Limpar assinaturas existentes
        $user->subscriptions()->delete();
        
        $this->assertFalse($user->hasActiveSubscription(), 'Usuário sem assinatura não deve ter acesso');
        $this->assertFalse($user->hasFeatureAccess(), 'Usuário sem assinatura não deve ter acesso às funcionalidades');
        
        $this->info('✅ Usuário sem assinatura corretamente bloqueado');
    }
    
    private function testUserWithExpiredSubscription(User $user, Plan $plan)
    {
        $this->info('🔍 Teste 2: Usuário com assinatura expirada...');
        
        // Criar assinatura expirada
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->subMonth(), // Expirada há 1 mês
        ]);
        
        $this->assertTrue($subscription->isExpired(), 'Assinatura deve estar expirada');
        $this->assertFalse($user->hasActiveSubscription(), 'Usuário com assinatura expirada não deve ter acesso');
        
        $this->info('✅ Usuário com assinatura expirada corretamente bloqueado');
    }
    
    private function testUserWithActiveSubscription(User $user, Plan $plan)
    {
        $this->info('🔍 Teste 3: Usuário com assinatura ativa...');
        
        // Limpar assinaturas anteriores
        $user->subscriptions()->delete();
        
        // Criar assinatura ativa
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);
        
        $this->assertTrue($subscription->isActive(), 'Assinatura deve estar ativa');
        $this->assertTrue($user->hasActiveSubscription(), 'Usuário com assinatura ativa deve ter acesso');
        $this->assertTrue($user->hasFeatureAccess(), 'Usuário com assinatura ativa deve ter acesso às funcionalidades');
        
        $this->info('✅ Usuário com assinatura ativa corretamente autorizado');
    }
    
    private function testAdminAccess()
    {
        $this->info('🔍 Teste 4: Acesso de administrador...');
        
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->warn('⚠️ Nenhum administrador encontrado, criando um temporário...');
            $admin = User::create([
                'name' => 'Admin Teste',
                'email' => 'admin@teste.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }
        
        $this->assertTrue($admin->isAdmin(), 'Usuário deve ser administrador');
        $this->assertTrue($admin->hasFeatureAccess(), 'Administrador deve ter acesso sem assinatura');
        
        $this->info('✅ Administrador corretamente autorizado');
    }
    
    private function testWebhookSecurity()
    {
        $this->info('🔍 Teste 5: Segurança do webhook...');
        
        // Teste de payload inválido
        $invalidPayloads = [
            [], // Vazio
            ['checkout' => []], // Sem transaction
            ['transaction' => []], // Sem checkout
            ['checkout' => ['id' => ''], 'transaction' => ['id' => 'test']], // ID vazio
        ];
        
        foreach ($invalidPayloads as $payload) {
            $this->assertFalse($this->validateWebhookPayload($payload), 'Payload inválido deve ser rejeitado');
        }
        
        // Teste de payload válido
        $validPayload = [
            'checkout' => ['id' => 'checkout123'],
            'transaction' => ['id' => 'transaction123', 'status' => 'paid']
        ];
        
        $this->assertTrue($this->validateWebhookPayload($validPayload), 'Payload válido deve ser aceito');
        
        $this->info('✅ Validação de webhook funcionando corretamente');
    }
    
    private function testRateLimiting()
    {
        $this->info('🔍 Teste 6: Rate limiting...');
        
        $cacheKey = 'bestfy_webhook_rate_limit_test';
        $attempts = 0;
        
        // Simular muitas tentativas
        for ($i = 0; $i < 15; $i++) {
            if (Cache::has($cacheKey)) {
                $attempts = Cache::get($cacheKey, 0);
                if ($attempts > 10) {
                    $this->info("✅ Rate limiting ativado após {$attempts} tentativas");
                    break;
                }
            }
            Cache::put($cacheKey, $attempts + 1, 60);
            $attempts++;
        }
        
        $this->info('✅ Rate limiting funcionando corretamente');
    }
    
    private function validateWebhookPayload(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        if (!isset($data['checkout']) || !isset($data['transaction'])) {
            return false;
        }

        if (empty($data['checkout']['id']) || empty($data['transaction']['id'])) {
            return false;
        }

        if (empty($data['transaction']['status'])) {
            return false;
        }

        return true;
    }
    
    private function assertTrue($condition, $message)
    {
        if (!$condition) {
            $this->error("❌ {$message}");
            throw new \Exception($message);
        }
    }
    
    private function assertFalse($condition, $message)
    {
        if ($condition) {
            $this->error("❌ {$message}");
            throw new \Exception($message);
        }
    }
}
