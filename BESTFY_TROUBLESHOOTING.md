# Troubleshooting - Integração Bestfy

## Erro: Token inválido (RL-2)

### Causa
A chave secreta da API está incorreta, inválida ou não está autorizada para o ambiente atual.

### Soluções

#### 1. Verificar Chaves da API
Certifique-se de que está usando as chaves corretas:

**Produção:**
```
sk_live_...
pk_live_...
```

**Sandbox/Teste:**
```
sk_test_...
pk_test_...
```

#### 2. Obter Chaves Válidas
1. Acesse o painel da Bestfy: https://dashboard.bestfybr.com.br
2. Faça login na sua conta
3. Vá em **Configurações** > **API Keys**
4. Copie as chaves **Secret Key** e **Public Key**
5. Atualize o arquivo `.env` com as chaves corretas

#### 3. Atualizar Arquivo .env
```env
BESTFY_BASE_URL=https://api.bestfybr.com.br/v1
BESTFY_SECRET_KEY=sua_chave_secreta_aqui
BESTFY_PUBLIC_KEY=sua_chave_publica_aqui
```

#### 4. Limpar Cache
Após atualizar as chaves, limpe o cache do Laravel:
```bash
php artisan config:clear
php artisan cache:clear
```

#### 5. Testar Conexão
```bash
php artisan bestfy:test
```

---

## Erro: 403 CloudFront

### Causa
O CloudFront (CDN da AWS) está bloqueando a requisição.

### Soluções

1. **Verificar Headers**: Certifique-se de enviar os headers corretos
2. **User-Agent**: Adicione um User-Agent válido nas requisições
3. **Rate Limiting**: Verifique se não está fazendo muitas requisições
4. **IP Bloqueado**: Entre em contato com o suporte da Bestfy

---

## Testando a Integração

### Comando de Teste
```bash
php artisan bestfy:test
```

Este comando irá:
- ✓ Verificar as configurações
- ✓ Tentar criar um checkout de teste
- ✓ Exibir erros detalhados

### Logs
Para ver logs detalhados, verifique:
```bash
tail -f storage/logs/laravel.log
```

---

## Checklist de Verificação

- [ ] Chaves da API estão corretas no `.env`
- [ ] Cache do Laravel foi limpo (`php artisan config:clear`)
- [ ] Planos foram criados no banco (`php artisan db:seed --class=PlanSeeder`)
- [ ] Usuário existe no banco de dados
- [ ] Conexão com a internet está funcionando
- [ ] Firewall não está bloqueando requisições HTTPS

---

## Contato com Suporte

Se o problema persistir após verificar todos os itens acima:

1. **Suporte Bestfy**: suporte@bestfybr.com.br
2. **Documentação**: https://docs.bestfybr.com.br
3. **Status da API**: https://status.bestfybr.com.br

---

## Notas Importantes

- As chaves `sk_live_` e `pk_live_` fornecidas no exemplo podem ser chaves de demonstração
- **NUNCA** compartilhe suas chaves secretas publicamente
- Use chaves de teste (`sk_test_`) para desenvolvimento
- Use chaves de produção (`sk_live_`) apenas em produção

