# HiLeads - Sistema de Extração de Contatos WhatsApp

## Descrição
O HiLeads é um sistema desenvolvido para extrair contatos de grupos do WhatsApp e gerenciar campanhas de marketing direto. O sistema permite conectar contas do WhatsApp, sincronizar grupos, extrair contatos e criar campanhas automatizadas.

## Funcionalidades

### 🔗 Conexão WhatsApp
- Conectar múltiplas contas do WhatsApp
- Geração de QR Code para autenticação
- Sincronização automática de grupos
- Status de conexão em tempo real

### 📱 Gerenciamento de Grupos
- Lista todos os grupos do WhatsApp conectado
- Visualização de participantes por grupo
- Extração de contatos dos grupos
- Histórico de extrações

### 👥 Gestão de Contatos
- Lista de contatos extraídos
- Status dos contatos (novo, contatado, interessado, etc.)
- Observações personalizadas
- Filtros por grupo e status

### 📢 Campanhas de Marketing
- Criação de campanhas personalizadas
- Seleção de contatos específicos
- Agendamento de envios
- Relatórios de performance
- Controle de status (rascunho, ativa, pausada, concluída)

## Tecnologias Utilizadas

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Tailwind CSS, Blade Templates
- **Banco de Dados**: MySQL
- **API WhatsApp**: Wuzapi
- **Autenticação**: Laravel Auth

## Instalação

### Pré-requisitos
- PHP 8.2 ou superior
- Composer
- Node.js 20.19+ ou 22.12+
- MySQL 5.7+
- NPM/Yarn

### Configuração

1. **Clone o repositório**
```bash
git clone <repository-url>
cd wpp
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Instale as dependências Node.js**
```bash
npm install
```

4. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados no .env**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wpp
DB_USERNAME=root
DB_PASSWORD=123
```

6. **Configure a API Wuzapi no .env**
```env
WUZAPI_BASE_URL=https://api.wuzapi.com
WUZAPI_TOKEN=seu_token_aqui
```

7. **Execute as migrations**
```bash
php artisan migrate
```

8. **Compile os assets**
```bash
npm run build
```

9. **Inicie o servidor**
```bash
php artisan serve
```

## Uso

### 1. Acesso ao Sistema
- Acesse `http://localhost:8000`
- Registre uma nova conta ou faça login
- Você será redirecionado para o dashboard

### 2. Conectar WhatsApp
- Vá para "WhatsApp" no menu
- Clique em "Nova Conexão"
- Digite o número do WhatsApp (formato internacional)
- Escaneie o QR Code gerado
- Aguarde a confirmação de conexão

### 3. Sincronizar Grupos
- Após conectar, vá para a página da conexão
- Clique em "Sincronizar Grupos"
- Aguarde a sincronização dos grupos

### 4. Extrair Contatos
- Vá para "Contatos" no menu
- Selecione um grupo
- Clique em "Extrair Contatos"
- Os contatos serão extraídos e salvos

### 5. Criar Campanhas
- Vá para "Campanhas" no menu
- Clique em "Nova Campanha"
- Preencha os dados da campanha
- Selecione os contatos desejados
- Agende ou inicie a campanha

## Estrutura do Banco de Dados

### Tabelas Principais
- `users` - Usuários do sistema
- `whatsapp_connections` - Conexões WhatsApp
- `whatsapp_groups` - Grupos sincronizados
- `extracted_contacts` - Contatos extraídos
- `campaigns` - Campanhas de marketing

## API Wuzapi

O sistema utiliza a API Wuzapi para integração com WhatsApp. Configure seus tokens no arquivo `.env`:

```env
WUZAPI_BASE_URL=http://localhost:8080
WUZAPI_TOKEN=seu_token_wuzapi
WUZAPI_ADMIN_TOKEN=seu_admin_token
```

### Endpoints Utilizados
- `/session/connect` - Conectar ao WhatsApp
- `/session/status` - Status da sessão
- `/session/qr` - Obter QR Code
- `/user/contacts` - Listar contatos
- `/user/info` - Informações do usuário
- `/chat/send/text` - Enviar mensagem de texto

### Configuração da Wuzapi
Consulte o arquivo `WUZAPI_SETUP.md` para instruções detalhadas de instalação e configuração da Wuzapi.

## Segurança

- Autenticação obrigatória para todas as rotas
- Policies para controle de acesso aos recursos
- Validação de dados em todas as entradas
- Sanitização de dados de saída

## Monitoramento

O sistema inclui:
- Logs de atividades
- Status de conexões
- Relatórios de campanhas
- Métricas de performance

## Suporte

Para suporte técnico ou dúvidas sobre o sistema, consulte a documentação ou entre em contato com a equipe de desenvolvimento.

## Licença

Este projeto é proprietário e destinado ao uso interno da empresa.