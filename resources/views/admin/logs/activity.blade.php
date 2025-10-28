@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Logs de Atividade</h1>
            <p class="text-muted-foreground mt-1">Visualize atividades dos utilizadores no sistema</p>
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
                   class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Sistema
                </a>
                <a href="{{ route('admin.logs.activity') }}" 
                   class="py-4 px-1 border-b-2 border-primary font-medium text-sm text-primary">
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
                        <label class="block text-sm font-medium text-foreground mb-2">Utilizador</label>
                        <select name="user_id" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Todos os utilizadores</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Ação</label>
                        <select name="action" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Todas as ações</option>
                            @foreach($actions as $key => $label)
                                <option value="{{ $key }}" {{ $action == $key ? 'selected' : '' }}>
                                    {{ $label }}
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
                               placeholder="Pesquisar nas atividades..." 
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

            <!-- Activity Logs Table -->
            <div class="bg-card rounded-lg border border-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Timestamp</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Utilizador</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Ação</th>
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
                                        @if($log['context'] && isset($log['context']['user_id']))
                                            @php
                                                $user = $users->firstWhere('id', $log['context']['user_id']);
                                            @endphp
                                            @if($user)
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">{{ $user->name }}</p>
                                                    <p class="text-xs text-muted-foreground">{{ $user->email }}</p>
                                                </div>
                                            @else
                                                <p class="text-sm text-muted-foreground">Utilizador #{{ $log['context']['user_id'] }}</p>
                                            @endif
                                        @else
                                            <span class="text-muted-foreground text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($log['context'] && isset($log['context']['action']))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                                {{ ucfirst($log['context']['action']) }}
                                            </span>
                                        @else
                                            <span class="text-muted-foreground text-sm">-</span>
                                        @endif
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                                        Nenhuma atividade encontrada para os filtros selecionados.
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
                <h3 class="text-lg font-semibold text-foreground">Contexto da Atividade</h3>
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
