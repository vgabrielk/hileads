# 🔍 Debug de Campanhas de Imagem

Este guia contém todas as ferramentas e logs implementados para diagnosticar e corrigir problemas no envio de imagens em campanhas.

## 📋 Problemas Identificados

Com base na análise dos logs existentes, identifiquei os seguintes problemas:

1. **Erro principal**: `"no session"` - A API do WhatsApp não está conectada
2. **Base64 está sendo processado corretamente** - Os logs mostram que o Base64 está sendo validado e armazenado
3. **Falta de logs detalhados** no envio da imagem para a API
4. **Validação de Base64 pode ser melhorada** para detectar problemas de integridade

## 🛠️ Ferramentas Implementadas

### 1. CampaignLogger Helper
**Arquivo**: `app/Helpers/CampaignLogger.php`

Sistema de logs específico para campanhas que registra:
- Início e fim de processos
- Validação de dados
- Dados de mídia (com sanitização)
- Chamadas de API
- Operações de banco de dados
- Erros e warnings

**Logs salvos em**: `storage/logs/create.log`

### 2. Logs Detalhados Implementados

#### MassSendingRequest (`app/Http/Requests/MassSendingRequest.php`)
- ✅ Validação de Base64 com verificação de integridade
- ✅ Logs de cada etapa da validação
- ✅ Detecção de problemas de formato e tamanho

#### MassSendingController (`app/Http/Controllers/MassSendingController.php`)
- ✅ Logs de criação de campanhas
- ✅ Processamento de dados de mídia
- ✅ Operações de banco de dados

#### WuzapiService (`app/Services/WuzapiService.php`)
- ✅ Validação de Base64 antes do envio
- ✅ Logs detalhados de chamadas de API
- ✅ Tratamento de erros específicos

#### ProcessMassSendingJob (`app/Jobs/ProcessMassSendingJob.php`)
- ✅ Logs de processamento de mídia
- ✅ Respostas da API
- ✅ Status de envio

## 🧪 Scripts de Teste

### 1. Teste Completo de Campanhas
```bash
php test_image_campaign.php
```

**O que testa**:
- Criação de imagem Base64 de teste
- Validação de diferentes formatos de Base64
- Criação de MassSending no banco
- Verificação de dados salvos
- Teste do WuzapiService

### 2. Teste de Conexão WhatsApp
```bash
php test_whatsapp_connection.php
```

**O que testa**:
- Status da sessão WhatsApp
- Tentativa de conexão
- Envio de mensagem de texto
- Envio de imagem
- Identificação do erro "no session"

### 3. Monitor de Logs em Tempo Real
```bash
php monitor_campaign_logs.php
```

**O que faz**:
- Monitora `storage/logs/create.log` em tempo real
- Mostra novos logs conforme são gerados
- Útil para debug durante testes

## 📊 Como Usar

### Passo 1: Executar Teste de Conexão
```bash
php test_whatsapp_connection.php
```

Este teste vai identificar se o problema é de conexão WhatsApp.

### Passo 2: Se Conexão OK, Testar Campanha
```bash
php test_image_campaign.php
```

Este teste vai verificar se o problema está no processamento de Base64.

### Passo 3: Monitorar Logs Durante Teste Real
Em outro terminal:
```bash
php monitor_campaign_logs.php
```

### Passo 4: Criar Campanha Real
1. Acesse a interface web
2. Crie uma campanha de imagem
3. Monitore os logs em tempo real
4. Analise os logs gerados

## 🔍 Análise de Logs

### Logs Importantes a Observar

1. **Validação de Base64**:
   ```
   [INFO] Validando formato do Base64
   [INFO] Validando integridade do Base64
   ```

2. **Criação de MassSending**:
   ```
   [DATABASE] Criando MassSending no banco de dados
   [DATABASE] MassSending criado com sucesso
   ```

3. **Envio para API**:
   ```
   [API] Enviando requisição para API Wuzapi
   [API] Resposta da API Wuzapi
   ```

4. **Erros de Sessão**:
   ```
   [ERROR] Falha ao enviar mensagem
   "no session"
   ```

### Comandos Úteis

```bash
# Ver logs recentes
tail -n 50 storage/logs/create.log

# Filtrar apenas erros
grep "ERROR" storage/logs/create.log

# Filtrar logs de API
grep "API" storage/logs/create.log

# Filtrar logs de mídia
grep "MEDIA" storage/logs/create.log

# Monitorar em tempo real
tail -f storage/logs/create.log
```

## 🚨 Problemas Comuns e Soluções

### 1. Erro "no session"
**Causa**: WhatsApp não está conectado ou logado
**Solução**:
- Verificar se o WhatsApp está conectado na interface web
- Escanear QR Code se necessário
- Verificar token da API

### 2. Base64 Inválido
**Causa**: Dados corrompidos ou formato incorreto
**Solução**:
- Verificar se o frontend está enviando data URL completo
- Verificar se não há truncamento no banco de dados

### 3. Arquivo Muito Grande
**Causa**: Base64 excede limite de 5MB
**Solução**:
- Reduzir tamanho da imagem
- Implementar compressão

### 4. Falha Silenciosa
**Causa**: Erro não capturado ou logado
**Solução**:
- Verificar logs detalhados implementados
- Usar scripts de teste para isolar problema

## 📈 Próximos Passos

1. **Execute os testes** para identificar o problema específico
2. **Monitore os logs** durante criação de campanhas reais
3. **Analise os resultados** dos logs detalhados
4. **Implemente correções** baseadas nos achados
5. **Teste novamente** para confirmar correção

## 🔧 Arquivos Modificados

- `app/Helpers/CampaignLogger.php` - Sistema de logs
- `app/Http/Requests/MassSendingRequest.php` - Validação com logs
- `app/Http/Controllers/MassSendingController.php` - Logs de criação
- `app/Services/WuzapiService.php` - Logs de API e validação
- `app/Jobs/ProcessMassSendingJob.php` - Logs de processamento

## 📝 Notas Importantes

- Todos os logs são salvos em `storage/logs/create.log`
- Logs incluem sanitização de dados sensíveis (Base64 é truncado)
- Scripts de teste criam dados temporários que são limpos automaticamente
- Logs são estruturados para facilitar análise e debug
