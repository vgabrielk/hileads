<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubscriptionSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar planos de teste
        $this->createTestPlans();
    }

    private function createTestPlans(): void
    {
        Plan::create([
            'name' => 'Plano Básico',
            'description' => 'Plano básico para testes',
            'price' => 29.90,
            'price_cents' => 2990,
            'interval' => 'monthly',
            'interval_count' => 1,
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Plano Premium',
            'description' => 'Plano premium para testes',
            'price' => 59.90,
            'price_cents' => 5990,
            'interval' => 'monthly',
            'interval_count' => 1,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function user_without_subscription_cannot_access_protected_features()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $this->actingAs($user);
        
        // Tentar acessar funcionalidades protegidas
        $response = $this->get('/whatsapp');
        $response->assertRedirect('/plans');
        $response->assertSessionHas('error');
        
        $response = $this->get('/mass-sendings');
        $response->assertRedirect('/plans');
        $response->assertSessionHas('error');
        
        $response = $this->get('/groups');
        $response->assertRedirect('/plans');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_with_expired_subscription_cannot_access_protected_features()
    {
        $user = User::factory()->create(['role' => 'user']);
        $plan = Plan::first();
        
        // Criar assinatura expirada
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->subMonth(), // Expirada há 1 mês
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get('/whatsapp');
        $response->assertRedirect('/plans');
        $response->assertSessionHas('error', 'Sua assinatura expirou. Renove para continuar usando o sistema.');
    }

    /** @test */
    public function user_with_cancelled_subscription_cannot_access_protected_features()
    {
        $user = User::factory()->create(['role' => 'user']);
        $plan = Plan::first();
        
        // Criar assinatura cancelada
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'cancelled',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addMonth(),
            'cancelled_at' => now()->subWeek(),
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get('/whatsapp');
        $response->assertRedirect('/plans');
        $response->assertSessionHas('error', 'Sua assinatura não está ativa. Entre em contato com o suporte.');
    }

    /** @test */
    public function user_with_active_subscription_can_access_protected_features()
    {
        $user = User::factory()->create(['role' => 'user']);
        $plan = Plan::first();
        
        // Criar assinatura ativa
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get('/whatsapp');
        $response->assertStatus(200);
        
        $response = $this->get('/mass-sendings');
        $response->assertStatus(200);
        
        $response = $this->get('/groups');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_user_can_access_protected_features_without_subscription()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $this->actingAs($admin);
        
        $response = $this->get('/whatsapp');
        $response->assertStatus(200);
        
        $response = $this->get('/mass-sendings');
        $response->assertStatus(200);
        
        $response = $this->get('/groups');
        $response->assertStatus(200);
    }

    /** @test */
    public function webhook_requires_valid_payload_structure()
    {
        // Payload inválido - sem checkout
        $response = $this->postJson('/bestfy/webhook', [
            'transaction' => ['id' => 'test123', 'status' => 'paid']
        ]);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid payload structure']);

        // Payload inválido - sem transaction
        $response = $this->postJson('/bestfy/webhook', [
            'checkout' => ['id' => 'test123']
        ]);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid payload structure']);

        // Payload inválido - sem IDs
        $response = $this->postJson('/bestfy/webhook', [
            'checkout' => [],
            'transaction' => ['status' => 'paid']
        ]);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid payload structure']);
    }

    /** @test */
    public function webhook_prevents_duplicate_processing()
    {
        $user = User::factory()->create();
        $plan = Plan::first();
        
        // Criar assinatura
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'bestfy_checkout_id' => 'checkout123',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $payload = [
            'checkout' => ['id' => 'checkout123'],
            'transaction' => ['id' => 'transaction123', 'status' => 'paid']
        ];

        // Primeira chamada
        $response = $this->postJson('/bestfy/webhook', $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Segunda chamada (deve ser ignorada)
        $response = $this->postJson('/bestfy/webhook', $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'already_processed']);
    }

    /** @test */
    public function webhook_handles_unknown_subscription_gracefully()
    {
        $payload = [
            'checkout' => ['id' => 'unknown_checkout'],
            'transaction' => ['id' => 'transaction123', 'status' => 'paid']
        ];

        $response = $this->postJson('/bestfy/webhook', $payload);
        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
    }

    /** @test */
    public function webhook_activates_subscription_correctly()
    {
        $user = User::factory()->create();
        $plan = Plan::first();
        
        // Criar assinatura pendente
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'bestfy_checkout_id' => 'checkout123',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $payload = [
            'checkout' => ['id' => 'checkout123'],
            'transaction' => ['id' => 'transaction123', 'status' => 'paid']
        ];

        $response = $this->postJson('/bestfy/webhook', $payload);
        $response->assertStatus(200);

        // Verificar se a assinatura foi ativada
        $subscription->refresh();
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals('transaction123', $subscription->bestfy_transaction_id);
    }

    /** @test */
    public function webhook_cancels_subscription_correctly()
    {
        $user = User::factory()->create();
        $plan = Plan::first();
        
        // Criar assinatura pendente
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'bestfy_checkout_id' => 'checkout123',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $payload = [
            'checkout' => ['id' => 'checkout123'],
            'transaction' => ['id' => 'transaction123', 'status' => 'cancelled']
        ];

        $response = $this->postJson('/bestfy/webhook', $payload);
        $response->assertStatus(200);

        // Verificar se a assinatura foi cancelada
        $subscription->refresh();
        $this->assertEquals('cancelled', $subscription->status);
        $this->assertNotNull($subscription->cancelled_at);
    }

    /** @test */
    public function webhook_prevents_multiple_active_subscriptions()
    {
        $user = User::factory()->create();
        $plan1 = Plan::first();
        $plan2 = Plan::skip(1)->first();
        
        // Criar primeira assinatura ativa
        $existingSubscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan1->id,
            'status' => 'active',
            'starts_at' => now()->subWeek(),
            'expires_at' => now()->addMonth(),
        ]);

        // Criar segunda assinatura pendente
        $newSubscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan2->id,
            'status' => 'pending',
            'bestfy_checkout_id' => 'checkout456',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $payload = [
            'checkout' => ['id' => 'checkout456'],
            'transaction' => ['id' => 'transaction456', 'status' => 'paid']
        ];

        $response = $this->postJson('/bestfy/webhook', $payload);
        $response->assertStatus(200);

        // Verificar se a assinatura antiga foi cancelada
        $existingSubscription->refresh();
        $this->assertEquals('cancelled', $existingSubscription->status);

        // Verificar se a nova assinatura foi ativada
        $newSubscription->refresh();
        $this->assertEquals('active', $newSubscription->status);
    }

    /** @test */
    public function webhook_rate_limiting_works()
    {
        $user = User::factory()->create();
        $plan = Plan::first();
        
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'bestfy_checkout_id' => 'checkout123',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $payload = [
            'checkout' => ['id' => 'checkout123'],
            'transaction' => ['id' => 'transaction123', 'status' => 'paid']
        ];

        // Fazer muitas requisições rapidamente
        for ($i = 0; $i < 15; $i++) {
            $response = $this->postJson('/bestfy/webhook', $payload);
            
            if ($i >= 10) {
                $response->assertStatus(429);
            }
        }
    }

    /** @test */
    public function subscription_security_middleware_logs_access_attempts()
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Subscription security check failed: No active subscription', \Mockery::type('array'));

        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
        
        $this->get('/whatsapp');
    }

    /** @test */
    public function postback_url_generation_is_secure()
    {
        $user = User::factory()->create();
        $plan = Plan::first();
        
        $this->actingAs($user);
        
        $response = $this->post("/plans/{$plan->id}/checkout");
        
        // Verificar se o token foi armazenado no cache
        $this->assertTrue(Cache::has('postback_token_' . substr(hash('sha256', $user->id . $plan->id . now()->timestamp . config('app.key')), 0, 10) . '*'));
    }
}
