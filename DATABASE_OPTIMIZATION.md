# Otimizações de Banco de Dados - WhatsApp Project

## Problemas Identificados e Soluções

### 1. Erro de Memória MySQL (SQLSTATE[HY001])
**Problema**: `Out of sort memory, consider increasing server sort buffer size`

**Soluções Implementadas**:
- ✅ Criado comando `php artisan db:optimize` para aumentar buffers do MySQL
- ✅ Otimizado consultas usando `ORDER BY id` em vez de `ORDER BY created_at`
- ✅ Adicionados índices compostos para melhor performance
- ✅ Implementado middleware para monitorar consultas lentas

### 2. Classe Notification Não Encontrada
**Problema**: `Class "App\Models\Notification" not found`

**Soluções Implementadas**:
- ✅ Criado modelo `App\Models\Notification`
- ✅ Criada migração para tabela `notifications`
- ✅ Adicionados campos necessários: title, message, type, channels, status, etc.

### 3. Coluna total_recipients Não Encontrada
**Problema**: `Unknown column 'total_recipients' in 'field list'`

**Soluções Implementadas**:
- ✅ Criada migração para adicionar colunas faltantes na tabela `mass_sendings`
- ✅ Adicionadas colunas: `total_recipients`, `failed_count`, `message_type`, `media_data`, etc.
- ✅ Atualizado modelo `MassSending` com novos campos
- ✅ Adicionada relação com `WhatsAppConnection`

### 4. Coluna whatsapp_connection_id Não Encontrada em extracted_contacts
**Problema**: `Unknown column 'whatsapp_connection_id' in 'field list'`

**Soluções Implementadas**:
- ✅ Criada migração para adicionar `whatsapp_connection_id` na tabela `extracted_contacts`
- ✅ Atualizado modelo `ExtractedContact` com novo campo e relação
- ✅ Adicionado índice para melhor performance

### 5. Coluna last_login_at Não Encontrada em users
**Problema**: `Unknown column 'last_login_at' in 'where clause'`

**Soluções Implementadas**:
- ✅ Criada migração para adicionar `last_login_at` na tabela `users`
- ✅ Atualizado modelo `User` com novo campo e cast apropriado

## Melhorias de Performance

### Índices Adicionados
```sql
-- Tabela mass_sendings
INDEX (status, created_at)
INDEX (user_id, created_at)
INDEX (message_type)

-- Tabela notifications
INDEX (user_id, status)
INDEX (status, created_at)
INDEX (type)
```

### Configurações MySQL Otimizadas
```sql
sort_buffer_size = 16MB
read_buffer_size = 8MB
read_rnd_buffer_size = 16MB
join_buffer_size = 8MB
tmp_table_size = 64MB
max_heap_table_size = 64MB
```

### Consultas Otimizadas
- Uso de `SELECT` específico em vez de `SELECT *`
- `ORDER BY id` em vez de `ORDER BY created_at` para melhor performance
- Uso de `COALESCE` para tratar valores NULL
- Paginação otimizada com índices apropriados

## Comandos Úteis

### Otimizar Banco de Dados
```bash
php artisan db:optimize
```

### Executar Migrações
```bash
php artisan migrate
```

### Verificar Status das Migrações
```bash
php artisan migrate:status
```

## Monitoramento

### Consultas Lentas
O middleware `OptimizeQueries` monitora automaticamente consultas que demoram mais de 1 segundo nas rotas admin e registra no log.

### Logs de Performance
```bash
tail -f storage/logs/laravel.log | grep "Slow queries"
```

## Próximos Passos Recomendados

1. **Cache de Consultas**: Implementar cache Redis para consultas frequentes
2. **Paginação Avançada**: Usar cursor-based pagination para grandes datasets
3. **Índices Adicionais**: Monitorar e adicionar índices conforme necessário
4. **Limpeza de Dados**: Implementar limpeza automática de dados antigos
5. **Monitoramento**: Configurar alertas para consultas lentas

## Estrutura da Tabela mass_sendings

```sql
CREATE TABLE mass_sendings (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    whatsapp_connection_id BIGINT NULL,
    name VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    message_type VARCHAR(255) DEFAULT 'text',
    media_data JSON NULL,
    status VARCHAR(255) DEFAULT 'draft',
    contact_ids JSON NULL,
    wuzapi_participants JSON NULL,
    total_contacts INT DEFAULT 0,
    total_recipients INT DEFAULT 0,
    sent_count INT DEFAULT 0,
    delivered_count INT DEFAULT 0,
    read_count INT DEFAULT 0,
    replied_count INT DEFAULT 0,
    failed_count INT DEFAULT 0,
    scheduled_at TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_status_created (status, created_at),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_message_type (message_type),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (whatsapp_connection_id) REFERENCES whatsapp_connections(id) ON DELETE SET NULL
);
```
