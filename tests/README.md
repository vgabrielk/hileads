# WhatsApp Mass Sending System - Load Testing Suite

Este conjunto de ferramentas de teste de carga foi criado para determinar quantos usuários simultâneos o sistema consegue suportar sem "dar merda" 😄.

## 🚀 Testes Disponíveis

### 1. Teste Rápido (Quick Test)
```bash
php quick_test.php
```
- Testa saúde básica do sistema
- Simula 10 e 25 usuários simultâneos
- Verifica conectividade com WhatsApp API
- Duração: ~2 minutos

### 2. Teste Completo de Carga
```bash
./run_load_test.sh [max_users] [duration] [monitor_interval]
```

**Exemplos:**
```bash
# Teste básico (50 usuários, 60 segundos)
./run_load_test.sh

# Teste pesado (100 usuários, 120 segundos)
./run_load_test.sh 100 120

# Teste extremo (200 usuários, 180 segundos)
./run_load_test.sh 200 180
```

### 3. Testes Individuais

#### Teste de Carga Principal
```bash
php load_test.php [max_users] [duration]
```

#### Monitor de Sistema
```bash
php system_monitor.php [interval_seconds]
```

#### Teste da API WhatsApp
```bash
php whatsapp_api_test.php
```

## 📊 O Que é Testado

### 🔍 Testes de Sistema
- **Conexão com banco de dados**
- **Resposta do servidor web**
- **Uso de memória e CPU**
- **Conexões simultâneas**
- **Tempo de resposta das páginas**

### 👥 Testes de Usuários Simultâneos
- **Login de usuários**
- **Visualização do dashboard**
- **Criação de campanhas**
- **Envio de mensagens**
- **Visualização de contatos**

### 📱 Testes da API WhatsApp
- **Status da conexão**
- **Envio de mensagens individuais**
- **Envio em massa (bulk)**
- **Limites de taxa (rate limits)**
- **Sessões simultâneas**

## 📈 Métricas Analisadas

### Performance
- **RPS (Requests Per Second)**
- **Tempo de resposta médio**
- **Taxa de sucesso**
- **Taxa de erro**

### Recursos do Sistema
- **Uso de memória (RAM)**
- **Uso de CPU**
- **Conexões do banco de dados**
- **Uso de disco**

### Capacidade
- **Máximo de usuários simultâneos**
- **Máximo de mensagens por segundo**
- **Máximo de campanhas simultâneas**

## 🎯 Cenários de Teste

### 1. Carga Leve (10 usuários)
- Teste básico de funcionalidade
- Verifica se o sistema funciona normalmente
- Duração: 30 segundos

### 2. Carga Média (50 usuários)
- Simula uso normal do sistema
- Testa performance em condições típicas
- Duração: 60 segundos

### 3. Carga Pesada (100 usuários)
- Teste de estresse
- Identifica gargalos de performance
- Duração: 120 segundos

### 4. Carga Extrema (200+ usuários)
- Teste de quebra
- Encontra o limite máximo do sistema
- Duração: 180 segundos

## 📋 Relatórios Gerados

### Estrutura de Arquivos
```
results/
├── load_test_YYYYMMDD_HHMMSS/
│   ├── load_test.log              # Log do teste de carga
│   ├── whatsapp_api_test.log      # Log do teste da API
│   ├── system_monitor.log         # Log do monitor de sistema
│   ├── combined_report.txt        # Relatório combinado
│   └── system_performance_report.txt # Relatório de performance
```

### Métricas Incluídas
- **Resumo executivo**
- **Gráficos de performance**
- **Análise de gargalos**
- **Recomendações de otimização**
- **Limites identificados**

## ⚙️ Configuração

### Arquivo de Configuração (`config.php`)
```php
return [
    'test' => [
        'base_url' => 'http://127.0.0.1:8000',
        'max_users' => 50,
        'test_duration' => 60,
    ],
    'database' => [
        'host' => 'localhost',
        'database' => 'hileads',
        // ...
    ],
    'whatsapp' => [
        'api_key' => 'your-api-key',
        'timeout' => 30,
        // ...
    ]
];
```

### Variáveis de Ambiente
```bash
# .env
LOAD_TEST_MAX_USERS=50
LOAD_TEST_DURATION=60
WUZAPI_API_KEY=your-api-key
```

## 🚨 Limites Identificados

### Limites de Hardware
- **RAM**: Sistema precisa de pelo menos 2GB para 50 usuários
- **CPU**: Processador com 2+ cores recomendado
- **Rede**: Conexão estável para API do WhatsApp

### Limites de Software
- **PHP**: Limite de memória (memory_limit)
- **MySQL**: Máximo de conexões (max_connections)
- **Apache/Nginx**: Limite de workers/processes

### Limites da API WhatsApp
- **Rate Limits**: Máximo de mensagens por minuto
- **Concurrent Sessions**: Máximo de sessões simultâneas
- **Message Size**: Tamanho máximo das mensagens

## 💡 Recomendações de Otimização

### Para Suportar Mais Usuários
1. **Cache**: Implementar Redis para cache
2. **Queue**: Usar filas para processamento assíncrono
3. **CDN**: Usar CDN para assets estáticos
4. **Load Balancer**: Distribuir carga entre servidores
5. **Database**: Otimizar queries e índices

### Para Melhor Performance
1. **OPcache**: Habilitar cache de PHP
2. **Database Pooling**: Usar pool de conexões
3. **Compression**: Comprimir respostas HTTP
4. **Minification**: Minificar CSS/JS
5. **Image Optimization**: Otimizar imagens

## 🔧 Troubleshooting

### Problemas Comuns
- **Out of Memory**: Aumentar memory_limit no PHP
- **Database Connection**: Verificar max_connections no MySQL
- **Timeout**: Aumentar timeout da API do WhatsApp
- **Permission Denied**: Verificar permissões dos arquivos

### Logs Importantes
- **Laravel Logs**: `storage/logs/laravel.log`
- **PHP Error Log**: `/var/log/php_errors.log`
- **MySQL Log**: `/var/log/mysql/error.log`
- **Apache/Nginx Log**: `/var/log/apache2/error.log`

## 📞 Suporte

Se encontrar problemas durante os testes:

1. **Verifique os logs** em `results/`
2. **Confirme a configuração** em `config.php`
3. **Teste individualmente** cada componente
4. **Monitore recursos** do sistema durante os testes

## 🎉 Resultados Esperados

### Sistema Saudável
- ✅ Taxa de sucesso > 95%
- ✅ Tempo de resposta < 2 segundos
- ✅ Uso de memória < 512MB
- ✅ CPU < 80%

### Sistema com Problemas
- ❌ Taxa de sucesso < 90%
- ❌ Tempo de resposta > 5 segundos
- ❌ Uso de memória > 1GB
- ❌ CPU > 90%

---

**Lembre-se**: Estes testes são para identificar limites e gargalos. Use os resultados para otimizar o sistema antes de colocar em produção! 🚀
