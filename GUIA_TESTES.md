# 🧪 Guia de Testes - Sistema HiLeads

## 🔐 Credenciais de Acesso

### Usuário Administrador
```
Email: admin@hileads.com
Senha: admin123
Token: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365
```

### Usuário Normal (Teste)
```
Email: user@hileads.com
Senha: user123
Token: 4b92a068c5095151f022a2febb513f70dc750268347917abc374a0031775f6a1
```

---

## 🎯 Checklist de Testes

### 1. Teste de Autenticação Web ✓

#### Login como Admin
```
1. Acesse: http://localhost:8000/login
2. Email: admin@hileads.com
3. Senha: admin123
4. Deve redirecionar para /dashboard
```

#### Login como Usuário Normal
```
1. Acesse: http://localhost:8000/login
2. Email: user@hileads.com
3. Senha: user123
4. Deve redirecionar para /dashboard
```

#### Registro de Novo Usuário
```
1. Acesse: http://localhost:8000/register
2. Preencha os dados
3. Sistema deve gerar token automaticamente
4. Redirecionar para /dashboard
```

---

### 2. Teste de Perfil e Token ✓

#### Visualizar Token
```
1. Faça login
2. Acesse: http://localhost:8000/profile
3. Deve mostrar seu token único
4. Botão "Copiar" deve funcionar
5. Exemplos de uso devem estar visíveis
```

#### Regenerar Token
```
1. Na página de perfil
2. Clique em "Regenerar Token"
3. Confirme a ação
4. Novo token deve ser gerado
5. Token antigo fica inválido
```

---

### 3. Teste de Conexão WhatsApp ✓

#### Como Usuário Normal
```
1. Faça login com user@hileads.com
2. Acesse: http://localhost:8000/whatsapp
3. Clique em "Conectar WhatsApp"
4. QR Code deve ser gerado com SEU token
5. Escaneie com seu WhatsApp
6. Status deve mudar para "Connected"
```

#### Verificação de Token Único
```bash
# Teste com token do usuário normal
curl -X GET http://localhost:8080/session/status \
  -H "token: 4b92a068c5095151f022a2febb513f70dc750268347917abc374a0031775f6a1"

# Teste com token do admin (deve ser sessão diferente)
curl -X GET http://localhost:8080/session/status \
  -H "token: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365"
```

---

### 4. Teste de API Admin ✓

#### Listar Usuários
```bash
curl -X GET http://localhost:8000/admin/users \
  -H "Authorization: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365" \
  -H "Content-Type: application/json"
```

**Esperado:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Administrador",
      "email": "admin@hileads.com",
      "role": "admin",
      "created_at": "..."
    },
    {
      "id": 2,
      "name": "Usuário Teste",
      "email": "user@hileads.com",
      "role": "user",
      "created_at": "..."
    }
  ]
}
```

#### Criar Novo Usuário com Token Customizado
```bash
curl -X POST http://localhost:8000/admin/users \
  -H "Authorization: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "senha123",
    "token": "TOKEN_CUSTOMIZADO_JOAO_123"
  }'
```

#### Criar Novo Usuário com Token Automático
```bash
curl -X POST http://localhost:8000/admin/users \
  -H "Authorization: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Maria Santos",
    "email": "maria@example.com",
    "password": "senha456"
  }'
```

#### Visualizar Usuário Específico
```bash
curl -X GET http://localhost:8000/admin/users/1 \
  -H "Authorization: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365"
```

#### Atualizar Usuário
```bash
curl -X PUT http://localhost:8000/admin/users/2 \
  -H "Authorization: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Usuário Teste Atualizado",
    "role": "admin"
  }'
```

#### Regenerar Token de Usuário
```bash
curl -X POST http://localhost:8000/admin/users/2/regenerate-token \
  -H "Authorization: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365"
```

#### Deletar Usuário
```bash
curl -X DELETE http://localhost:8000/admin/users/2 \
  -H "Authorization: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365"
```

#### Listar Usuários da Wuzapi (Admin)
```bash
curl -X GET http://localhost:8000/admin/wuzapi-users \
  -H "Authorization: 682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365"
```

**Resposta Esperada:**
```json
{
  "success": true,
  "data": [
    {
      "wuzapi": {
        "connected": true,
        "events": "All",
        "id": "bec45bb93cbd24cbec32941ec3c93a12",
        "jid": "5491155551122:12@s.whatsapp.net",
        "loggedIn": true,
        "name": "Some User",
        "token": "682b069dac3919e81ee2de52a8fabf0995e3f7e7eca010183a740bdea4f42365"
      },
      "laravel": {
        "id": 1,
        "name": "Administrador",
        "email": "admin@hileads.com",
        "role": "admin"
      }
    }
  ]
}
```

---

### 5. Teste de Integração Wuzapi ✓

#### Conectar ao WhatsApp
```bash
# Com token do usuário normal
curl -X POST http://localhost:8080/session/connect \
  -H "token: 4b92a068c5095151f022a2febb513f70dc750268347917abc374a0031775f6a1" \
  -H "Content-Type: application/json" \
  -d '{"Subscribe": ["Message", "ChatPresence"], "Immediate": true}'
```

#### Obter QR Code
```bash
curl -X GET http://localhost:8080/session/qr \
  -H "token: 4b92a068c5095151f022a2febb513f70dc750268347917abc374a0031775f6a1"
```

#### Verificar Status da Sessão
```bash
curl -X GET http://localhost:8080/session/status \
  -H "token: 4b92a068c5095151f022a2febb513f70dc750268347917abc374a0031775f6a1"
```

#### Listar Contatos
```bash
curl -X GET http://localhost:8080/user/contacts \
  -H "token: 4b92a068c5095151f022a2febb513f70dc750268347917abc374a0031775f6a1"
```

#### Enviar Mensagem
```bash
curl -X POST http://localhost:8080/chat/send/text \
  -H "token: 4b92a068c5095151f022a2febb513f70dc750268347917abc374a0031775f6a1" \
  -H "Content-Type: application/json" \
  -d '{
    "Phone": "5511999999999",
    "Body": "Olá! Mensagem de teste do HiLeads."
  }'
```

---

### 6. Teste de Segurança ✓

#### Acessar Admin sem Token
```bash
curl -X GET http://localhost:8000/admin/users
# Esperado: 401 Unauthorized
```

#### Acessar Admin com Token de Usuário Normal
```bash
curl -X GET http://localhost:8000/admin/users \
  -H "Authorization: 4b92a068c5095151f022a2febb513f70dc750268347917abc374a0031775f6a1"
# Esperado: 403 Forbidden (não é admin)
```

#### Token Inválido
```bash
curl -X GET http://localhost:8000/admin/users \
  -H "Authorization: TOKEN_INVALIDO_123"
# Esperado: 401 Unauthorized
```

---

## 📊 Resultados Esperados

### ✅ Sucesso
- Usuários criados com tokens únicos
- Login funcionando para ambos os usuários
- Tokens visíveis na página de perfil
- Conexão WhatsApp usando token do usuário logado
- API admin protegida e funcionando
- Sessões WhatsApp isoladas por usuário

### ❌ Problemas Comuns

#### "Token de autenticação não fornecido"
- Certifique-se de enviar o header `Authorization` com o token

#### "Apenas administradores podem acessar"
- Verifique se está usando o token do admin, não do usuário normal

#### "Wuzapi não responde"
- Verifique se a Wuzapi está rodando: `curl http://localhost:8080/`

#### "QR Code não aparece"
- Verifique se conectou primeiro: `/session/connect`
- Depois pegue o QR: `/session/qr`

---

## 🚀 Fluxo Completo de Teste

### Cenário: Novo usuário conectando WhatsApp

```
1. Acesse http://localhost:8000/register
   - Nome: Teste User
   - Email: teste@test.com
   - Senha: teste123

2. Após registro, acesse http://localhost:8000/profile
   - Copie seu token único

3. Acesse http://localhost:8000/whatsapp
   - Clique em "Conectar WhatsApp"
   - QR Code será gerado com SEU token

4. Escaneie o QR Code com seu WhatsApp
   - Aguarde conexão

5. Volte para /whatsapp
   - Status deve mostrar "Connected"

6. Agora você pode:
   - Listar contatos
   - Enviar mensagens
   - Sincronizar grupos
```

---

## 📝 Anotações de Teste

Use este espaço para anotar seus resultados:

```
[ ] Login Admin funcionou
[ ] Login User funcionou
[ ] Tokens gerados corretamente
[ ] Perfil mostra token
[ ] QR Code gerado
[ ] WhatsApp conectado
[ ] API Admin lista usuários
[ ] API Admin cria usuário
[ ] Sessões isoladas funcionam
```

---

## 🎯 Próximos Testes

Após validar os testes acima:
1. Teste com múltiplos usuários simultâneos
2. Teste regeneração de token e impacto na sessão
3. Teste criação via API com diferentes roles
4. Teste envio de mensagens
5. Teste extração de contatos

---

**Boa sorte nos testes! 🚀**

Se encontrar algum problema, verifique:
- Logs: `storage/logs/laravel.log`
- Status Wuzapi: `http://localhost:8080/`
- Banco de dados: credenciais no `.env`

