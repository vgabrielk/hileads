@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Logs e Auditoria</h1>
            <p class="text-muted-foreground mt-1">Visualize e faça a gestão logs do sistema</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.logs.clear') }}" class="inline">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" 
                        onclick="return confirm('Tem certeza que deseja limpar todos os logs? Esta ação não pode ser desfeita.')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Limpar Logs
                </button>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-card rounded-lg border border-border">
        <div class="border-b border-border">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('admin.logs.index') }}" 
                   class="py-4 px-1 border-b-2 border-primary font-medium text-sm text-primary">
                    Todos os Logs
                </a>
                <a href="{{ route('admin.logs.system') }}" 
                   class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
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
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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
                        <label class="block text-sm font-medium text-foreground mb-2">Procurar</label>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Pesquisar nos logs..." 
                               class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Por Página</label>
                        <select name="per_page" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                        </select>
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

            <!-- Logs Table -->
            <div class="bg-card rounded-lg border border-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Timestamp</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Nível</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Mensagem</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Contexto</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Ações</th>
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
                                        <p class="text-sm text-foreground">{{ Str::limit($log['message'], 100) }}</p>
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
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.logs.show', ['date' => $date, 'index' => $loop->index, 'type' => 'laravel']) }}" 
                                               class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-primary hover:bg-primary/10 rounded transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Ver Detalhes
                                            </a>
                                            <button onclick="showRawLog('{{ $log['timestamp'] }}')" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-muted-foreground hover:bg-muted/50 rounded transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Raw
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                                        Nenhum log encontrado para os filtros selecionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Log Files Info -->
            @if($logFiles->count() > 0)
            <div class="mt-6 bg-card rounded-lg border border-border p-4">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ficheiros de Log Disponíveis</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($logFiles as $file)
                        <div class="border border-border rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-foreground">{{ $file['name'] }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ number_format($file['size'] / 1024, 2) }} KB
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ $file['modified']->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.logs.download', ['file' => $file['name']]) }}" 
                                   class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-primary hover:bg-primary/10 rounded transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Baixar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
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

<!-- Raw Log Modal -->
<div id="rawLogModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-card rounded-lg border border-border max-w-4xl w-full max-h-96 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Log Raw</h3>
                <button onclick="closeRawLogModal()" class="text-muted-foreground hover:text-foreground">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 overflow-auto">
                <pre id="rawLogContent" class="text-sm text-foreground whitespace-pre-wrap"></pre>
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

function showRawLog(timestamp) {
    const log = logsData.find(l => l.timestamp === timestamp);
    if (log) {
        document.getElementById('rawLogContent').textContent = log.raw;
        document.getElementById('rawLogModal').classList.remove('hidden');
    }
}

function closeContextModal() {
    document.getElementById('contextModal').classList.add('hidden');
}

function closeRawLogModal() {
    document.getElementById('rawLogModal').classList.add('hidden');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeContextModal();
        closeRawLogModal();
    }
});
</script>
@endsection
