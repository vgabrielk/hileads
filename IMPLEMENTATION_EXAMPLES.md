# Exemplos de Implementação - Carregamento Assíncrono

## 📚 Exemplos Práticos por Tipo de View

### 1. WhatsApp Index (Lista de Conexões)

#### Controller (`WhatsAppController.php`)
```php
public function index()
{
    return view('whatsapp.index');
}

public function getConnections()
{
    $user = auth()->user();
    $connections = $user->whatsappConnections()
        ->latest()
        ->get();
    
    $html = view('whatsapp.partials.connections-list', compact('connections'))->render();
    return response()->json(['html' => $html, 'data' => $connections]);
}
```

#### Rota (`routes/web.php`)
```php
Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
Route::get('/api/whatsapp/connections', [WhatsAppController::class, 'getConnections'])->name('api.whatsapp.connections');
```

#### View (`resources/views/whatsapp/index.blade.php`)
```blade
@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <h1 class="text-3xl font-bold">Conexões WhatsApp</h1>
    
    <div id="connections-container" 
         data-async-load="{{ route('api.whatsapp.connections') }}" 
         data-async-cache="true" 
         data-async-cache-duration="120000">
        <!-- Skeleton Loaders -->
        <div class="space-y-4">
            @for($i = 0; $i < 3; $i++)
                <x-skeleton-list-item />
            @endfor
        </div>
    </div>
</div>
@endsection
```

#### Partial (`resources/views/whatsapp/partials/connections-list.blade.php`)
```blade
<div class="space-y-4">
    @forelse($connections as $connection)
        <div class="bg-card border rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold">{{ $connection->phone_number }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $connection->instance_id }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs 
                    {{ $connection->status === 'active' ? 'bg-success/10 text-success' : 'bg-destructive/10 text-destructive' }}">
                    {{ $connection->status === 'active' ? 'Conectado' : 'Desconectado' }}
                </span>
            </div>
        </div>
    @empty
        <p class="text-center text-muted-foreground">Nenhuma conexão encontrada</p>
    @endforelse
</div>
```

---

### 2. Admin Dashboard

#### Controller (`AdminDashboardController.php`)
```php
public function index()
{
    return view('admin.dashboard');
}

public function getStats()
{
    $stats = [
        'total_users' => User::count(),
        'active_subscriptions' => Subscription::active()->count(),
        'total_revenue' => Subscription::sum('amount'),
        'active_campaigns' => MassSending::active()->count(),
    ];
    
    $html = view('admin.dashboard.partials.stats', compact('stats'))->render();
    return response()->json(['html' => $html, 'data' => $stats]);
}

public function getRecentActivity()
{
    $activities = ActivityLog::latest()->take(10)->get();
    
    $html = view('admin.dashboard.partials.activity', compact('activities'))->render();
    return response()->json(['html' => $html, 'data' => $activities]);
}
```

#### Rota
```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/api/admin/dashboard/stats', [AdminDashboardController::class, 'getStats'])->name('api.admin.dashboard.stats');
    Route::get('/api/admin/dashboard/activity', [AdminDashboardController::class, 'getRecentActivity'])->name('api.admin.dashboard.activity');
});
```

#### View (`resources/views/admin/dashboard.blade.php`)
```blade
@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <h1 class="text-3xl font-bold">Admin Dashboard</h1>
    
    <!-- Stats Cards -->
    <div id="stats-container" 
         data-async-load="{{ route('api.admin.dashboard.stats') }}" 
         data-async-cache="true" 
         data-async-cache-duration="300000">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @for($i = 0; $i < 4; $i++)
                <x-skeleton-card />
            @endfor
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="bg-card rounded-lg border p-6">
        <h2 class="text-xl font-semibold mb-4">Atividade Recente</h2>
        <div id="activity-container" 
             data-async-load="{{ route('api.admin.dashboard.activity') }}" 
             data-async-cache="false">
            @for($i = 0; $i < 5; $i++)
                <x-skeleton-list-item />
            @endfor
        </div>
    </div>
</div>
@endsection
```

---

### 3. Mass Sendings (Campanhas)

#### Controller (`MassSendingController.php`)
```php
public function index()
{
    return view('mass-sendings.index');
}

public function getCampaigns()
{
    $user = auth()->user();
    $campaigns = $user->massSendings()
        ->with('group')
        ->latest()
        ->paginate(15);
    
    $html = view('mass-sendings.partials.campaigns-list', compact('campaigns'))->render();
    return response()->json(['html' => $html, 'data' => $campaigns]);
}
```

#### View com Alpine.js para Atualização em Tempo Real
```blade
@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6" x-data="campaignsData()" x-init="init()">
    <h1 class="text-3xl font-bold">Campanhas de Envio</h1>
    
    <div id="campaigns-container" 
         data-async-load="{{ route('api.mass-sendings.list') }}" 
         data-async-cache="false">
        <div class="space-y-4">
            @for($i = 0; $i < 5; $i++)
                <div class="bg-card border rounded-lg p-4 animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
            @endfor
        </div>
    </div>
</div>

<script>
function campaignsData() {
    return {
        campaigns: [],
        refreshInterval: null,
        
        init() {
            // Atualizar a cada 10 segundos
            this.refreshInterval = setInterval(() => {
                this.loadCampaigns();
            }, 10000);
        },
        
        loadCampaigns() {
            window.asyncLoader.load(
                '{{ route('api.mass-sendings.list') }}', 
                '#campaigns-container',
                { cache: false }
            );
        }
    }
}
</script>
@endsection
```

#### Partial com Barra de Progresso
```blade
<!-- resources/views/mass-sendings/partials/campaigns-list.blade.php -->
@forelse($campaigns as $campaign)
    <div class="bg-card border rounded-lg p-4">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold">{{ $campaign->name }}</h3>
                <p class="text-sm text-muted-foreground">{{ $campaign->group->name }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-medium
                @if($campaign->status === 'completed') bg-success/10 text-success
                @elseif($campaign->status === 'running') bg-primary/10 text-primary
                @elseif($campaign->status === 'paused') bg-warning/10 text-warning
                @else bg-muted text-muted-foreground @endif">
                {{ ucfirst($campaign->status) }}
            </span>
        </div>
        
        <!-- Barra de Progresso -->
        @if($campaign->status === 'running' || $campaign->status === 'completed')
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div class="bg-primary h-2 rounded-full transition-all duration-300" 
                     style="width: {{ $campaign->progress_percentage }}%"></div>
            </div>
            <p class="text-xs text-muted-foreground">
                {{ $campaign->sent_count }} / {{ $campaign->total_count }} enviados
                ({{ number_format($campaign->progress_percentage, 1) }}%)
            </p>
        @endif
        
        <div class="mt-3 flex gap-2">
            @if($campaign->status === 'pending')
                <button onclick="startCampaign({{ $campaign->id }})" 
                        class="px-3 py-1 text-sm bg-primary text-white rounded hover:bg-primary/90">
                    Iniciar
                </button>
            @endif
            @if($campaign->status === 'running')
                <button onclick="pauseCampaign({{ $campaign->id }})" 
                        class="px-3 py-1 text-sm bg-warning text-white rounded hover:bg-warning/90">
                    Pausar
                </button>
            @endif
            <a href="{{ route('mass-sendings.show', $campaign) }}" 
               class="px-3 py-1 text-sm bg-secondary rounded hover:bg-secondary/80">
                Detalhes
            </a>
        </div>
    </div>
@empty
    <p class="text-center text-muted-foreground">Nenhuma campanha encontrada</p>
@endforelse
```

---

### 4. Groups (Grupos)

#### Controller
```php
public function index()
{
    return view('groups.index');
}

public function getGroups()
{
    $user = auth()->user();
    $service = new WuzapiService($user->api_token);
    
    $groupsResponse = $service->getGroups();
    $groups = [];
    
    if ($groupsResponse['success'] ?? false) {
        $groups = collect($groupsResponse['data'] ?? [])->map(function($group) {
            return [
                'name' => $group['Name'] ?? 'Grupo sem nome',
                'jid' => $group['JID'] ?? '',
                'participants_count' => count($group['Participants'] ?? []),
                'is_announce' => $group['IsAnnounce'] ?? false,
            ];
        });
    }
    
    $html = view('groups.partials.groups-grid', compact('groups'))->render();
    return response()->json(['html' => $html, 'data' => $groups]);
}
```

---

### 5. Admin Users (Usuários)

#### Controller
```php
public function index()
{
    return view('admin.users.index');
}

public function getUsers(Request $request)
{
    $search = $request->get('search', '');
    
    $users = User::query()
        ->when($search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })
        ->with(['activeSubscription.plan'])
        ->latest()
        ->paginate(20);
    
    $html = view('admin.users.partials.users-table', compact('users'))->render();
    return response()->json(['html' => $html, 'data' => $users]);
}
```

---

## 🎯 Padrões Comuns de Uso

### Busca em Tempo Real
```blade
<div x-data="{ search: '' }">
    <input type="text" x-model="search" 
           @input.debounce.500ms="searchData()" 
           placeholder="Buscar...">
    
    <div id="results"></div>
</div>

<script>
function searchData() {
    const search = document.querySelector('input').value;
    const url = new URL('/api/search', window.location.origin);
    if (search) url.searchParams.set('q', search);
    
    window.asyncLoader.load(url.toString(), '#results', { cache: false });
}
</script>
```

### Paginação
```blade
<!-- Na Partial -->
<div class="flex justify-between items-center mt-4">
    <span>Página {{ $items->currentPage() }} de {{ $items->lastPage() }}</span>
    <div class="flex gap-2">
        @if($items->previousPageUrl())
            <button onclick="loadPage('{{ $items->previousPageUrl() }}')">Anterior</button>
        @endif
        @if($items->nextPageUrl())
            <button onclick="loadPage('{{ $items->nextPageUrl() }}')">Próxima</button>
        @endif
    </div>
</div>

<script>
function loadPage(url) {
    window.asyncLoader.load(url, '#container', { cache: false });
}
</script>
```

### Refresh Automático
```blade
<script>
// Atualizar a cada 30 segundos
setInterval(() => {
    window.asyncLoader.load('/api/data', '#container', { cache: false });
}, 30000);
</script>
```

---

## 📊 Checklist de Implementação

Para cada view que você quer converter:

- [ ] Criar método API no Controller retornando JSON com `html` e `data`
- [ ] Criar partial em `resources/views/[modulo]/partials/`
- [ ] Atualizar view principal com `data-async-load`
- [ ] Adicionar skeleton loaders apropriados
- [ ] Registrar rota API em `routes/web.php`
- [ ] Testar carregamento e cache
- [ ] Adicionar refresh button se necessário
- [ ] Implementar paginação se aplicável

---

## 🚀 Próximos Passos

1. **WhatsApp Views:** Implementar carregamento assíncrono para:
   - Lista de conexões
   - Detalhes de conexão
   - Chat em tempo real

2. **Admin Views:** Implementar para:
   - Dashboard admin
   - Gerenciamento de usuários
   - Gerenciamento de assinaturas
   - Analytics

3. **Real-time Updates:** Considerar implementar WebSockets para:
   - Status de campanhas
   - Novas mensagens no chat
   - Notificações em tempo real

---

**Todos os exemplos seguem o mesmo padrão:** View limpa → Carrega assíncrono → Mostra skeleton → Substitui com dados reais

**Tempo estimado por view:** 15-20 minutos para implementação completa

