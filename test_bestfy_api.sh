#!/bin/bash

echo "🔍 Testando API da Bestfy"
echo "================================"
echo ""

echo "📋 Chaves configuradas:"
echo "Secret Key: sk_live_your_bestfy_secret_key_here"
echo "Public Key: pk_live_your_bestfy_public_key_here"
echo ""

echo "🔄 Enviando requisição para criar checkout..."
echo ""

curl -X POST https://api.bestfybr.com.br/v1/checkouts \
  -u "sk_live_your_bestfy_secret_key_here:" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "amount": 2990,
    "items": [{
      "name": "Plano Básico",
      "description": "Teste de checkout",
      "quantity": 1,
      "price": 2990
    }],
    "settings": {
      "description": "Teste de integração",
      "requestAddress": true,
      "requestPhone": true,
      "traceable": true,
      "boleto": {"enabled": true},
      "pix": {"enabled": true},
      "card": {"enabled": true},
      "splits": [{
        "recipientId": "default",
        "percentage": 100
      }]
    }
  }' | jq '.'

echo ""
echo ""
echo "================================"
echo "✅ Se funcionou: A integração está OK!"
echo "❌ Se retornou erro 401: Chaves inválidas - entre em contato com Bestfy"
echo "❌ Se retornou erro 403: Problema de acesso - verifique permissões"

