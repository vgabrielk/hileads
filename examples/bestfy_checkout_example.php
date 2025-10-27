<?php

/**
 * Exemplo de uso da funcionalidade de checkout da Bestfy
 * 
 * Este arquivo demonstra como usar o BestfyService para criar checkouts
 * tanto para planos específicos quanto para checkouts genéricos.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\BestfyService;
use App\Models\Plan;
use App\Models\User;

// Exemplo 1: Criar checkout para um plano específico
function exemploCheckoutPlano()
{
    try {
        $bestfyService = new BestfyService();
        
        // Simular dados de um plano
        $plan = new Plan();
        $plan->id = 1;
        $plan->name = 'Plano Premium';
        $plan->price_cents = 2990; // R$ 29,90
        
        // Simular dados de um usuário
        $user = new User();
        $user->id = 1;
        $user->name = 'João Silva';
        $user->email = 'joao@exemplo.com';
        
        // URL de postback (opcional)
        $postbackUrl = 'https://seudominio.com/webhook/bestfy';
        
        // Criar checkout
        $checkout = $bestfyService->createCheckout($plan, $user, $postbackUrl);
        
        echo "Checkout criado com sucesso!\n";
        echo "ID: " . $checkout['id'] . "\n";
        echo "URL Segura: " . $checkout['secureUrl'] . "\n";
        
        return $checkout;
        
    } catch (Exception $e) {
        echo "Erro ao criar checkout: " . $e->getMessage() . "\n";
        return null;
    }
}

// Exemplo 2: Criar checkout genérico
function exemploCheckoutGenerico()
{
    try {
        $bestfyService = new BestfyService();
        
        // Dados do checkout genérico
        $checkoutData = [
            'amount' => 5000, // R$ 50,00 em centavos
            'items' => [
                [
                    'title' => 'Produto A',
                    'unitPrice' => 3000, // R$ 30,00
                    'quantity' => 1,
                    'tangible' => true,
                    'externalRef' => 'PROD-A-001'
                ],
                [
                    'title' => 'Produto B',
                    'unitPrice' => 2000, // R$ 20,00
                    'quantity' => 1,
                    'tangible' => true,
                    'externalRef' => 'PROD-B-001'
                ]
            ],
            'settings' => [
                'defaultPaymentMethod' => 'pix',
                'requestAddress' => true,
                'requestPhone' => true,
                'traceable' => true,
                'boleto' => [
                    'enabled' => true,
                    'expiresInDays' => 5
                ],
                'pix' => [
                    'enabled' => true,
                    'expiresInDays' => 1
                ],
                'card' => [
                    'enabled' => true,
                    'freeInstallments' => 3,
                    'maxInstallments' => 12
                ],
                'splits' => []
            ],
            'postbackUrl' => 'https://seudominio.com/webhook/bestfy',
            'description' => 'Venda de produtos diversos'
        ];
        
        // Criar checkout genérico
        $checkout = $bestfyService->createGenericCheckout($checkoutData);
        
        echo "Checkout genérico criado com sucesso!\n";
        echo "ID: " . $checkout['id'] . "\n";
        echo "URL Segura: " . $checkout['secureUrl'] . "\n";
        
        return $checkout;
        
    } catch (Exception $e) {
        echo "Erro ao criar checkout genérico: " . $e->getMessage() . "\n";
        return null;
    }
}

// Exemplo 3: Testar conexão com a API
function exemploTesteConexao()
{
    try {
        $bestfyService = new BestfyService();
        
        $resultado = $bestfyService->testConnection();
        
        if ($resultado['success']) {
            echo "Conexão com a API Bestfy: OK\n";
            echo "Status HTTP: " . $resultado['status'] . "\n";
        } else {
            echo "Erro na conexão: " . $resultado['error'] . "\n";
        }
        
        return $resultado;
        
    } catch (Exception $e) {
        echo "Erro ao testar conexão: " . $e->getMessage() . "\n";
        return null;
    }
}

// Exemplo 4: Buscar detalhes de um checkout
function exemploBuscarCheckout($checkoutId)
{
    try {
        $bestfyService = new BestfyService();
        
        $checkout = $bestfyService->getCheckout($checkoutId);
        
        echo "Detalhes do checkout:\n";
        echo "ID: " . $checkout['id'] . "\n";
        echo "Valor: R$ " . number_format($checkout['amount'] / 100, 2, ',', '.') . "\n";
        echo "Status: " . $checkout['status'] ?? 'N/A' . "\n";
        
        return $checkout;
        
    } catch (Exception $e) {
        echo "Erro ao buscar checkout: " . $e->getMessage() . "\n";
        return null;
    }
}

// Executar exemplos
echo "=== Exemplos de uso da API Bestfy ===\n\n";

echo "1. Testando conexão...\n";
exemploTesteConexao();

echo "\n2. Criando checkout para plano...\n";
$checkoutPlano = exemploCheckoutPlano();

echo "\n3. Criando checkout genérico...\n";
$checkoutGenerico = exemploCheckoutGenerico();

// Se algum checkout foi criado, buscar detalhes
if ($checkoutPlano && isset($checkoutPlano['id'])) {
    echo "\n4. Buscando detalhes do checkout do plano...\n";
    exemploBuscarCheckout($checkoutPlano['id']);
}

if ($checkoutGenerico && isset($checkoutGenerico['id'])) {
    echo "\n5. Buscando detalhes do checkout genérico...\n";
    exemploBuscarCheckout($checkoutGenerico['id']);
}

echo "\n=== Fim dos exemplos ===\n";
