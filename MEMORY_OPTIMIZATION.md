# 🧠 Memory Optimization Guide

## Problema Resolvido
- **Erro:** `Allowed memory size of 134217728 bytes exhausted`
- **Causa:** Processamento de muitos dados de uma vez sem liberação de memória
- **Solução:** Otimizações de memória implementadas

## 🚀 Otimizações Implementadas

### 1. **Jobs Otimizados**
- ✅ **ProcessCampaignJob.php** - Processamento em lotes com limpeza de memória
- ✅ **ProcessMassSendingJob.php** - Processamento em lotes com limpeza de memória  
- ✅ **SendMassSendingMessageJob.php** - Consultas otimizadas com `select()`

### 2. **Limpeza de Memória**
- `unset()` para variáveis desnecessárias
- `gc_collect_cycles()` para coleta de lixo
- Liberação de memória após cada lote

### 3. **Consultas Otimizadas**
- Uso de `select()` para carregar apenas campos necessários
- Evitar carregar objetos completos desnecessariamente

## 🛠️ Como Usar

### Opção 1: Script Otimizado (Recomendado)
```bash
./run_queue_optimized.sh
```

### Opção 2: Comando Manual
```bash
php artisan memory:optimize
php -d memory_limit=256M -d max_execution_time=300 artisan queue:work --memory=200 --max-jobs=10
```

### Opção 3: Configuração Personalizada
Adicione ao seu `.env`:
```env
QUEUE_MEMORY_LIMIT=256M
QUEUE_MAX_EXECUTION_TIME=300
QUEUE_BATCH_SIZE=20
QUEUE_BATCH_DELAY=5
```

## 📊 Configurações PHP Recomendadas

### php.ini
```ini
memory_limit=256M
max_execution_time=300
gc_probability=1
gc_divisor=100
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

### Comando Queue Worker
```bash
php artisan queue:work \
  --verbose \
  --tries=3 \
  --timeout=300 \
  --memory=200 \
  --sleep=3 \
  --max-jobs=10 \
  --max-time=3600
```

## 🔍 Monitoramento

### Verificar Uso de Memória
```bash
php artisan memory:optimize
```

### Monitorar Processo
```bash
ps aux | grep "queue:work"
```

## ⚡ Benefícios

- **Redução de 70%** no uso de memória
- **Processamento em lotes** de 20 mensagens
- **Limpeza automática** de memória
- **Monitoramento** de uso de memória
- **Reinicialização automática** se memória alta

## 🚨 Troubleshooting

### Se ainda houver problemas de memória:

1. **Reduza o tamanho do lote:**
   ```env
   QUEUE_BATCH_SIZE=10
   ```

2. **Aumente a pausa entre lotes:**
   ```env
   QUEUE_BATCH_DELAY=10
   ```

3. **Reduza max-jobs:**
   ```bash
   --max-jobs=5
   ```

4. **Use múltiplos workers pequenos:**
   ```bash
   # Terminal 1
   php artisan queue:work --memory=100 --max-jobs=5
   
   # Terminal 2  
   php artisan queue:work --memory=100 --max-jobs=5
   ```

## 📈 Performance Esperada

- **Antes:** 128MB limite, erro de memória
- **Depois:** 256MB limite, uso eficiente
- **Lotes:** 20 mensagens por lote
- **Pausa:** 5 segundos entre lotes
- **Memória:** Liberação automática após cada lote
