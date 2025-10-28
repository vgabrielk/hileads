@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Logs do Sistema</h1>
            <p class="text-muted-foreground mt-1">Visualize logs específicos do sistema</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.logs.clear') }}" class="inline">
                @csrf
                <input type="hidden" name="type" value="system">
                <button type="submit" 
                        onclick="return confirm('Tem certeza que deseja limpar os logs do sistema?')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Limpar Logs do Sistema
                </button>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-card rounded-lg border border-border">
        <div class="border-b border-border">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('admin.logs.index') }}" 
                   class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Todos os Logs
                </a>
                <a href="{{ route('admin.logs.system') }}" 
                   class="py-4 px-1 border-b-2 border-primary font-medium text-sm text-primary">
                    Sistema
                </a>
                <a href="{{ route('admin.logs.activity') }}" 
                   class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Atividade
                </a>
                <a href="{{ route('admin.logs.errors') }}" 
                   class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Erros
                </a>
            </nav>
        </div>

        <div class="p-6">
            <!-- Filters -->
            <div class="bg-muted/30 rounded-lg p-4 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Nível</label>
                        <select name="level" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="all" {{ $logLevel == 'all' ? 'selected' : '' }}>Todos</option>
                            @foreach($logLevels as $level)
                                <option value="{{ $level }}" {{ $logLevel == $level ? 'selected' : '' }}>
                                    {{ ucfirst($level) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Data</label>
                        <input type="date" name="date" value="{{ $date }}" 
                               class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Buscar</label>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Pesquisar nos logs..." 
                               class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- System Logs Table -->
            <div class="bg-card rounded-lg border border-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Timestamp</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Nível</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Mensagem</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Contexto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($logs as $log)
                                <tr class="hover:bg-muted/30">
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-foreground font-mono">{{ $log['timestamp'] }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($log['level'] === 'ERROR' || $log['level'] === 'CRITICAL' || $log['level'] === 'ALERT' || $log['level'] === 'EMERGENCY') bg-destructive/10 text-destructive
                                            @elseif($log['level'] === 'WARNING') bg-warning/10 text-warning
                                            @elseif($log['level'] === 'INFO') bg-primary/10 text-primary
                                            @elseif($log['level'] === 'DEBUG') bg-muted/10 text-muted-foreground
                                            @else bg-muted/10 text-muted-foreground @endif">
                                            {{ $log['level'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-foreground">{{ Str::limit($log['message'], 150) }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($log['context'])
                                            <button onclick="showContext('{{ $log['timestamp'] }}')" 
                                                    class="text-primary hover:text-primary/80 text-sm font-medium">
                                                Ver Contexto
                                            </button>
                                        @else
                                            <span class="text-muted-foreground text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">
                                        Nenhum log do sistema encontrado para os filtros selecionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Context Modal -->
<div id="contextModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-card rounded-lg border border-border max-w-2xl w-full max-h-96 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Contexto do Log</h3>
                <button onclick="closeContextModal()" class="text-muted-foreground hover:text-foreground">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 overflow-auto">
                <pre id="contextContent" class="text-sm text-foreground whitespace-pre-wrap"></pre>
            </div>
        </div>
    </div>
</div>

<script>
// Store logs data for modal display
const logsData = @json($logs);

function showContext(timestamp) {
    const log = logsData.find(l => l.timestamp === timestamp);
    if (log && log.context) {
        document.getElementById('contextContent').textContent = JSON.stringify(log.context, null, 2);
        document.getElementById('contextModal').classList.remove('hidden');
    }
}

function closeContextModal() {
    document.getElementById('contextModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeContextModal();
    }
});
</script>
@endsection
