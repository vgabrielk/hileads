# Testes de Validação de Mídia para MassSending

Este documento descreve os testes implementados para garantir a validação correta de dados de mídia em campanhas de envio em massa.

## Arquivos de Teste

### Testes Unitários
- `tests/Unit/MassSendingModelTest.php` - Testa métodos do modelo MassSending
- `tests/Unit/MassSendingRequestTest.php` - Testa validação de formulários
- `tests/Unit/MissingMediaExceptionTest.php` - Testa exceção customizada

### Testes de Integração
- `tests/Feature/MassSendingMediaTest.php` - Testa fluxo completo de criação e validação

### Factories e Fixtures
- `database/factories/MassSendingFactory.php` - Factory para criar dados de teste
- `tests/fixtures/valid_media_data.json` - Dados de mídia válidos para testes
- `tests/fixtures/invalid_media_data.json` - Dados de mídia inválidos para testes

## Como Executar os Testes

### Executar todos os testes de mídia
```bash
php artisan test --filter=MassSending
```

### Executar testes unitários específicos
```bash
php artisan test tests/Unit/MassSendingModelTest.php
php artisan test tests/Unit/MassSendingRequestTest.php
php artisan test tests/Unit/MissingMediaExceptionTest.php
```

### Executar testes de integração
```bash
php artisan test tests/Feature/MassSendingMediaTest.php
```

### Executar com cobertura de código
```bash
php artisan test --coverage --filter=MassSending
```

## Cenários de Teste Cobertos

### Validação de Modelo (MassSendingModelTest)
- ✅ Campanhas de texto não precisam de dados de mídia
- ✅ Validação de dados de mídia válidos para imagem, vídeo e documento
- ✅ Rejeição de dados de mídia vazios ou inválidos
- ✅ Validação de nome obrigatório para documentos
- ✅ Mecanismo de fallback para raw_media_data
- ✅ Serialização/deserialização correta de media_data

### Validação de Formulário (MassSendingRequestTest)
- ✅ Campanhas de texto válidas
- ✅ Campanhas de mídia com dados válidos
- ✅ Rejeição de campanhas de mídia sem dados
- ✅ Validação de formato JSON
- ✅ Validação de formato base64
- ✅ Validação de tamanho máximo (10MB)
- ✅ Validação de nome obrigatório para documentos
- ✅ Validação de mensagem obrigatória para texto

### Exceção Customizada (MissingMediaExceptionTest)
- ✅ Criação de exceção com contexto
- ✅ Renderização de resposta JSON
- ✅ Códigos de erro apropriados

### Fluxo Completo (MassSendingMediaTest)
- ✅ Criação bem-sucedida de campanhas de texto
- ✅ Criação bem-sucedida de campanhas de mídia
- ✅ Rejeição de campanhas inválidas
- ✅ Validação de modelo em tempo real
- ✅ Mecanismo de fallback
- ✅ Lançamento de exceções apropriadas

## Configuração de Ambiente

### Variáveis de Ambiente Necessárias
```env
# Para testes
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
LOG_CHANNEL=testing
```

### Dependências de Teste
- PHPUnit (já incluído no Laravel)
- RefreshDatabase trait para limpeza entre testes
- Queue::fake() para simular jobs

## Dados de Teste

### Dados Válidos
- Imagem JPEG com base64 válido
- Vídeo MP4 com base64 válido
- Documento PDF com base64 válido
- Todos incluem metadados necessários (nome, tipo, tamanho)

### Dados Inválidos
- Base64 vazio ou ausente
- Formato base64 inválido
- JSON malformado
- Documentos sem nome
- Tamanho excedendo limite

## Monitoramento e Logs

Os testes incluem logs detalhados para debug:
- Validação de dados de entrada
- Processamento de mídia
- Falhas de validação
- Uso de fallback

## Próximos Passos

1. Executar testes em CI/CD
2. Adicionar testes de performance para arquivos grandes
3. Implementar testes de integração com APIs externas
4. Adicionar testes de carga para múltiplas campanhas simultâneas
