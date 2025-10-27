# 🆘 Ajuda Necessária - Integração Bestfy

## Status Atual

✅ **Autenticação funcionando** (com chave atualizada e formato `:x`)
✅ **Payload básico correto** (amount, items, settings)
❌ **Campos dos objetos de pagamento desconhecidos**

## Problema

A API está retornando erro 422 dizendo que faltam campos obrigatórios nos objetos de pagamento (`boleto`, `pix`, `card`), mas a documentação não mostra quais campos cada um desses objetos precisa ter.

### Erros Recebidos:

1. Quando não envio `card`:
```
"Cartão habilitado deve ser do tipo boolean."
"Cartão habilitado é obrigatório."
"Número de parcelas sem juros deve ser um número inteiro."
"Número de parcelas sem juros é obrigatório."
"Máximo de parcelas deve ser um número inteiro"
"Máximo de parcelas é obrigatório."
```

2. Quando não envio `boleto`:
```
"Boleto habilitado deve ser do tipo boolean."
"Boleto habilitado é obrigatório."
```

## O Que Preciso

**Por favor, forneça um exemplo COMPLETO de payload que funciona** para criar um checkout, incluindo todos os campos dos objetos:
- `boleto object`
- `pix object`  
- `card object`

Ou acesse a documentação da API Bestfy e expanda esses objetos para ver os campos internos.

## Tentativas Já Feitas

```json
// Tentativa 1
"card": {
  "enabled": true,
  "maxInstallments": 12,
  "installmentsWithoutInterest": 1
}

// Tentativa 2
"card": {
  "enabled": true,
  "max_installments": 12,
  "installments_without_interest": 1
}

// Tentativa 3 (objetos vazios)
"boleto": {},
"pix": {},
"card": {}
```

Todas retornam erro 422.

## Solução Rápida

**Opção 1:** Me envie um exemplo real de JSON que funciona criando um checkout

**Opção 2:** Acesse https://dashboard.bestfybr.com.br e consulte um checkout já criado para ver o formato

**Opção 3:** Entre em contato com suporte@bestfybr.com.br e peça a documentação completa dos objetos de configuração de pagamento

## Código Atual

O código está 100% pronto, apenas faltam os nomes corretos dos campos!

```php
'settings' => [
    'defaultPaymentMethod' => 'pix',
    'requestAddress' => false,
    'requestPhone' => false,
    'traceable' => false,
    'boleto' => [
        // ??? Quais campos aqui?
    ],
    'pix' => [
        // ??? Quais campos aqui?
    ],
    'card' => [
        // ??? Quais campos aqui?
    ],
    'splits' => []
]
```

