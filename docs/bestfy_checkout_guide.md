# Guia de Uso - Checkout Bestfy

Este guia explica como usar a funcionalidade de checkout da API Bestfy implementada no `BestfyService`.

## Configuração

Certifique-se de que as seguintes configurações estão definidas no arquivo `.env`:

```env
BESTFY_BASE_URL=https://api.bestfybr.com.br/v1
BESTFY_SECRET_KEY=sua_chave_secreta_aqui
BESTFY_PUBLIC_KEY=sua_chave_publica_aqui
```

## Métodos Disponíveis

### 1. Criar Checkout para Plano

Use este método quando quiser criar um checkout para um plano específico (assinatura).

```php
use App\Services\BestfyService;
use App\Models\Plan;
use App\Models\User;

$bestfyService = new BestfyService();
$plan = Plan::find(1); // Seu plano
$user = User::find(1); // Seu usuário
$postbackUrl = 'https://seudominio.com/webhook/bestfy'; // Opcional

$checkout = $bestfyService->createCheckout($plan, $user, $postbackUrl);
```

**Resposta esperada:**
```php
[
    'id' => 123,
    'secureUrl' => 'https://checkout.bestfy.com.br/...',
    'secureId' => 'abc123',
    'amount' => 2990,
    'status' => 'pending'
]
```

### 2. Criar Checkout Genérico

Use este método para criar checkouts personalizados com múltiplos itens.

```php
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

$checkout = $bestfyService->createGenericCheckout($checkoutData);
```

### 3. Buscar Detalhes de um Checkout

```php
$checkoutId = '123';
$checkout = $bestfyService->getCheckout($checkoutId);
```

### 4. Testar Conexão

```php
$resultado = $bestfyService->testConnection();

if ($resultado['success']) {
    echo "Conexão OK";
} else {
    echo "Erro: " . $resultado['error'];
}
```

## Estrutura dos Dados

### Items (Obrigatório)

Cada item deve conter:

- `title` (string): Nome do produto/serviço
- `unitPrice` (int): Preço unitário em centavos
- `quantity` (int): Quantidade
- `tangible` (bool): Se é um produto físico
- `externalRef` (string, opcional): Referência externa

### Settings (Obrigatório)

Configurações obrigatórias:

- `defaultPaymentMethod` (string): `credit_card`, `boleto` ou `pix`
- `requestAddress` (bool): Solicitar endereço
- `requestPhone` (bool): Solicitar telefone
- `traceable` (bool): Gerenciar status de entrega

Configurações de pagamento (opcionais):

- `boleto.enabled` (bool): Habilitar boleto
- `boleto.expiresInDays` (int): Dias para expiração
- `pix.enabled` (bool): Habilitar PIX
- `pix.expiresInDays` (int): Dias para expiração
- `card.enabled` (bool): Habilitar cartão
- `card.freeInstallments` (int): Parcelas sem juros
- `card.maxInstallments` (int): Máximo de parcelas

## Tratamento de Erros

O serviço valida todos os dados antes de enviar para a API. Erros comuns:

- **Amount inválido**: Deve ser um inteiro positivo
- **Items vazios**: Pelo menos um item é obrigatório
- **Settings inválidos**: Configurações obrigatórias ausentes
- **Chave secreta não configurada**: Verifique o arquivo `.env`

## Webhooks

Para receber notificações de status do pagamento, configure uma URL de postback:

```php
$postbackUrl = 'https://seudominio.com/webhook/bestfy';
```

O webhook receberá dados no formato:

```json
{
    "checkout": {
        "id": 123
    },
    "transaction": {
        "id": 456,
        "status": "paid"
    }
}
```

## Logs

Todos os requests e responses são logados automaticamente. Verifique os logs em:

- `storage/logs/laravel.log`

## Exemplo Completo

Veja o arquivo `examples/bestfy_checkout_example.php` para exemplos completos de uso.

## Troubleshooting

### Erro de Conexão
- Verifique se a chave secreta está configurada
- Teste a conexão com `testConnection()`
- Verifique os logs para detalhes do erro

### Erro de Validação
- Verifique se todos os campos obrigatórios estão presentes
- Confirme que os tipos de dados estão corretos
- Valores monetários devem estar em centavos (inteiros)

### Erro 403 CloudFront
- A API pode estar temporariamente indisponível
- Tente novamente em alguns minutos
- Verifique se não há bloqueios de IP
