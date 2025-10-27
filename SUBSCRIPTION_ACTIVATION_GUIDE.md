# 🔧 Guia de Ativação de Assinaturas

## 🎯 **Problema Identificado**

As assinaturas estão sendo criadas com status `pending`, mas não são ativadas automaticamente após o pagamento. Isso acontece porque:

1. **Webhooks não estão configurados** no Stripe Dashboard
2. **Processamento automático** não está funcionando
3. **Sessões antigas** (live mode) não existem mais

## ✅ **Soluções Implementadas**

### **1. Comandos de Ativação Manual**

#### **Listar Assinaturas Pendentes:**
```bash
php artisan subscriptions:list-pending --limit=10
```

#### **Ativar Assinatura Específica:**
```bash
php artisan subscription:activate {ID}
```

#### **Processar Todas as Pendentes:**
```bash
php artisan subscriptions:process-pending
```

#### **Ativação Inteligente (Recomendado):**
```bash
# Verificar sem fazer mudanças
php artisan subscriptions:smart-activate --dry-run

# Ativar realmente
php artisan subscriptions:smart-activate
```

### **2. Sistema de Entitlements**

#### **Configurar Features:**
```bash
php artisan stripe:setup-entitlements
```

#### **Testar Entitlements:**
```bash
php artisan stripe:test-entitlements --user={USER_ID}
```

#### **Forçar Criação de Entitlements:**
```bash
php artisan stripe:force-entitlements
```

## 🚀 **Fluxo Recomendado para Novas Assinaturas**

### **1. Após Criar Nova Assinatura:**
```bash
# Verificar assinaturas pendentes
php artisan subscriptions:list-pending

# Processar automaticamente
php artisan subscriptions:smart-activate
```

### **2. Verificar Status:**
```bash
# Verificar assinaturas ativas
php artisan tinker --execute="echo App\Models\Subscription::where('status', 'active')->count();"
```

### **3. Testar Entitlements:**
```bash
# Testar para usuário específico
php artisan stripe:test-entitlements --user={USER_ID}
```

## 🔧 **Configuração de Webhooks (Recomendado)**

Para automatizar o processo, configure webhooks no Stripe Dashboard:

### **URL do Webhook:**
```
https://seudominio.com/stripe/webhook
```

### **Eventos Necessários:**
- `checkout.session.completed`
- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `invoice.payment_succeeded`
- `invoice.payment_failed`
- `entitlements.active_entitlement_summary.updated`

## 📊 **Status Atual do Sistema**

- ✅ **Assinaturas Ativas**: 3
- ✅ **Sistema de Entitlements**: Configurado
- ✅ **Comandos de Gerenciamento**: Implementados
- ✅ **Features por Plano**: Configuradas

## 🎯 **Comandos Úteis**

### **Verificar Status Geral:**
```bash
php artisan tinker --execute="
echo 'Total: ' . App\Models\Subscription::count() . PHP_EOL;
echo 'Active: ' . App\Models\Subscription::where('status', 'active')->count() . PHP_EOL;
echo 'Pending: ' . App\Models\Subscription::where('status', 'pending')->count() . PHP_EOL;
"
```

### **Ativar Assinatura por ID:**
```bash
php artisan subscription:activate 34
```

### **Processar Todas as Pendentes:**
```bash
php artisan subscriptions:process-pending
```

## ⚠️ **Problemas Conhecidos**

1. **Sessões Live Mode**: Sessões antigas em modo live não existem mais
2. **Webhooks**: Não configurados no Stripe Dashboard
3. **Ativação Automática**: Precisa ser manual por enquanto

## 🎉 **Resultado Final**

O sistema está funcionando! Agora você pode:

- ✅ **Criar assinaturas** com cartões de teste
- ✅ **Ativar manualmente** com comandos
- ✅ **Gerenciar entitlements** automaticamente
- ✅ **Verificar status** facilmente

**Para novas assinaturas, use sempre o comando `subscriptions:smart-activate` após o pagamento!** 🚀
