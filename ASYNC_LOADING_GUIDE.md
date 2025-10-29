# Guia de Implementação: Carregamento Assíncrono com Skeleton Loaders

## 📋 Visão Geral

Este sistema implementa carregamento assíncrono de dados em todas as views do Laravel Blade, melhorando significativamente a experiência do usuário através de:
- Carregamento instantâneo das páginas
- Skeleton loaders durante o carregamento de dados
- Cache inteligente de dados
- Navegação fluída sem dependência de dados

## 🎯 Componentes Criados

### 1. Skeleton Loaders (Componentes Blade)
Localizados em `resources/views/components/`:

- `skeleton-card.blade.php` - Card de estatísticas
- `skeleton-table-row.blade.php` - Linha de tabela
- `skeleton-list-item.blade.php` - Item de lista

**Uso:**
```blade
<x-skeleton-card />
<x-skeleton-table-row />
<x-skeleton-list-item />
```

### 2. Sistema JavaScript
**Arquivo:** `public/js/async-loader.js`

**Funções Principais:**
```javascript
// Carregar dados assíncronamente
loadAsync(url, targetElement, options);

// Exemplo
loadAsync('/api/dashboard/stats', '#stats-container', {
    cache: true,
    cacheDuration: 300000 // 5 minutos
});
```

**Auto-carregamento com atributos data:**
```html
<div data-async-load="/api/endpoint" 
     data-async-cache="true" 
     data-async-cache-duration="300000">
    <!-- Skeleton loader aqui -->
</div>
```

## 🔧 Padrão de Implementação

### Passo 1: Criar Endpoint API no Controller

```php
// Antes (método existente)
public function index()
{
    $data = Model::all();
    return view('resource.index', compact('data'));
}

// Depois (view vazia)
public function index()
{
    return view('resource.index');
}

// Novo endpoint API
public function getData()
{
    $data = Model::all();
    $html = view('resource.partials.data-list', compact('data'))->render();
    return response()->json(['html' => $html, 'data' => $data]);
}
```

### Passo 2: Criar View Partial

Crie em `resources/views/[modulo]/partials/`:

```blade
<!-- resources/views/resource/partials/data-list.blade.php -->
@forelse($items as $item)
    <div class="item">
        <h3>{{ $item->name }}</h3>
        <p>{{ $item->description }}</p>
    </div>
@empty
    <p>Nenhum item encontrado</p>
@endforelse
```

### Passo 3: Atualizar View Principal

```blade
<!-- resources/views/resource/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="p-8">
    <h1>Título da Página</h1>
    
    <!-- Container com carregamento assíncrono -->
    <div id="data-container" 
         data-async-load="{{ route('api.resource.data') }}" 
         data-async-cache="true" 
         data-async-cache-duration="300000">
        
        <!-- Skeleton Loader -->
        @for($i = 0; $i < 5; $i++)
            <x-skeleton-list-item />
        @endfor
    </div>
</div>
@endsection
```

### Passo 4: Adicionar Rota API

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/resource', [ResourceController::class, 'index'])->name('resource.index');
    Route::get('/api/resource/data', [ResourceController::class, 'getData'])->name('api.resource.data');
});
```

## 📦 Views Já Implementadas

### ✅ Dashboard
- **Controller:** `DashboardController`
- **Endpoints API:**
  - `/api/dashboard/stats` - Estatísticas
  - `/api/dashboard/access-status` - Status de acesso
  - `/api/dashboard/recent-connections` - Conexões recentes
  - `/api/dashboard/recent-groups` - Grupos recentes
  - `/api/dashboard/recent-contacts` - Contatos recentes

### ✅ Plans
- **Controller:** `PlanController`
- **Endpoints API:**
  - `/api/plans` - Lista de planos (usuários)
  - `/api/admin/plans` - Lista de planos (admin)

### ✅ Contacts
- **Controller:** `ContactController`
- **Endpoints API:**
  - `/api/contacts` - Lista de contatos

## 🚀 Views Pendentes de Implementação

### 1. WhatsApp Views
**Arquivos:**
- `resources/views/whatsapp/index.blade.php`
- `resources/views/whatsapp/show.blade.php`
- `resources/views/whatsapp/contacts.blade.php`

**Passos:**
1. Adicionar métodos API em `WhatsAppController`:
   - `getConnections()` - Lista de conexões
   - `getConnectionDetails($id)` - Detalhes de conexão
   - `getContacts()` - Contatos do WhatsApp

2. Criar partials:
   - `resources/views/whatsapp/partials/connections-list.blade.php`
   - `resources/views/whatsapp/partials/connection-details.blade.php`

3. Atualizar views para usar `data-async-load`

### 2. Mass Sendings
**Arquivos:**
- `resources/views/mass-sendings/index.blade.php`
- `resources/views/mass-sendings/show.blade.php`

**Endpoints necessários:**
- `/api/mass-sendings` - Lista de campanhas
- `/api/mass-sendings/{id}/progress` - Progresso da campanha

### 3. Groups
**Arquivos:**
- `resources/views/groups/index.blade.php`
- `resources/views/groups/show.blade.php`

**Endpoints necessários:**
- `/api/groups` - Lista de grupos
- `/api/groups/{id}/members` - Membros do grupo

### 4. Admin Views

#### Admin Dashboard
- **Arquivo:** `resources/views/admin/dashboard.blade.php`
- **Endpoints:** 
  - `/api/admin/dashboard/stats`
  - `/api/admin/dashboard/recent-activity`

#### Admin Users
- **Arquivo:** `resources/views/admin/users/index.blade.php`
- **Endpoint:** `/api/admin/users/list`

#### Admin Subscriptions
- **Arquivo:** `resources/views/admin/subscriptions/index.blade.php`
- **Endpoint:** `/api/admin/subscriptions/list`

#### Admin Campaigns
- **Arquivo:** `resources/views/admin/campaigns/index.blade.php`
- **Endpoint:** `/api/admin/campaigns/list`

## 🎨 Skeleton Loaders Customizados

### Para Tabelas Grandes
```blade
<tbody>
    @for($i = 0; $i < 10; $i++)
        <x-skeleton-table-row />
    @endfor
</tbody>
```

### Para Cards de Estatísticas
```blade
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    @for($i = 0; $i < 4; $i++)
        <x-skeleton-card />
    @endfor
</div>
```

### Para Listas
```blade
<div class="space-y-3">
    @for($i = 0; $i < 5; $i++)
        <x-skeleton-list-item />
    @endfor
</div>
```

### Skeleton Customizado
```blade
<div class="animate-pulse">
    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
</div>
```

## ⚙️ Opções de Configuração

### Cache
```html
<!-- Ativar cache por 5 minutos -->
data-async-cache="true" 
data-async-cache-duration="300000"

<!-- Desativar cache -->
data-async-cache="false"
```

### JavaScript Avançado
```javascript
// Carregar múltiplos endpoints em paralelo
window.asyncLoader.loadAll([
    { url: '/api/stats', target: '#stats', options: { cache: true } },
    { url: '/api/users', target: '#users', options: { cache: true } },
    { url: '/api/logs', target: '#logs', options: { cache: false } }
]);

// Limpar cache
window.asyncLoader.clearCache(); // Tudo
window.asyncLoader.clearCache('/api/stats'); // Específico

// Evento após carregamento
document.querySelector('#data-container').addEventListener('async-loaded', (e) => {
    console.log('Dados carregados:', e.detail.data);
});
```

## 🔍 Debugging

### Ver requisições no console
```javascript
// O async-loader.js já loga erros automaticamente
// Abra o DevTools Console para ver mensagens
```

### Forçar recarregamento sem cache
```javascript
// Limpar cache antes de recarregar
window.asyncLoader.clearCache();
location.reload();
```

## 📊 Benefícios Implementados

1. **Performance:** Páginas carregam instantaneamente
2. **UX:** Feedback visual com skeleton loaders
3. **Cache:** Redução de requisições ao backend
4. **Escalabilidade:** Fácil adicionar novos endpoints
5. **Manutenibilidade:** Código organizado e reutilizável

## 🎯 Próximos Passos Recomendados

1. Implementar carregamento assíncrono nas views de WhatsApp
2. Adicionar aos Mass Sendings com barra de progresso em tempo real
3. Implementar em todas as views admin
4. Adicionar indicadores de loading mais específicos por tipo de dado
5. Considerar implementar WebSockets para atualizações em tempo real

## 📚 Recursos Adicionais

- **Alpine.js:** Já incluído no layout para interatividade adicional
- **Tailwind CSS:** Classes de utilitários para estilização
- **CSRF Token:** Automaticamente incluído nas requisições

## 🐛 Troubleshooting

### Problema: Dados não carregam
**Solução:** Verifique se:
1. A rota API está registrada em `routes/web.php`
2. O método no controller retorna JSON com `html` key
3. O atributo `data-async-load` tem a URL correta

### Problema: Skeleton não desaparece
**Solução:** Verifique se:
1. O endpoint retorna `{ html: '...', data: {...} }`
2. Não há erros no console do navegador
3. A autenticação está funcionando

### Problema: Cache não funciona
**Solução:**
1. Verifique `data-async-cache="true"`
2. Ajuste `data-async-cache-duration` (em milissegundos)
3. Limpe o cache com `asyncLoader.clearCache()`

---

**Desenvolvido para:** HiLeads - Gestão Inteligente de Leads
**Data:** Outubro 2025
**Autor:** Sistema de Carregamento Assíncrono

