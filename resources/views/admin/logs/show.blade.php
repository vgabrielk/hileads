@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Detalhes do Log</h1>
            <p class="text-muted-foreground mt-1">Visualização completa do log selecionado</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.logs.index', ['date' => $date, 'type' => $type]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- Log Details -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Log Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($log['level'] === 'ERROR' || $log['level'] === 'CRITICAL' || $log['level'] === 'ALERT' || $log['level'] === 'EMERGENCY') bg-destructive/10 text-destructive
                        @elseif($log['level'] === 'WARNING') bg-warning/10 text-warning
                        @elseif($log['level'] === 'INFO') bg-primary/10 text-primary
                        @elseif($log['level'] === 'DEBUG') bg-muted/10 text-muted-foreground
                        @else bg-muted/10 text-muted-foreground @endif">
                        {{ $log['level'] }}
                    </span>
                    <span class="text-sm text-muted-foreground">{{ $log['environment'] }}</span>
                </div>
                <div class="text-sm text-muted-foreground font-mono">
                    {{ $log['timestamp'] }}
                </div>
            </div>

            <!-- Log Message -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-2">Mensagem</h3>
                <div class="bg-muted/30 rounded-lg p-4">
                    <p class="text-sm text-foreground whitespace-pre-wrap">{{ $log['message'] }}</p>
                </div>
            </div>

            <!-- Log Context -->
            @if($log['context'])
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-2">Contexto</h3>
                <div class="bg-muted/30 rounded-lg p-4">
                    <pre class="text-sm text-foreground overflow-x-auto"><code>{{ json_encode($log['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            @endif

            <!-- Raw Log -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-2">Log Bruto</h3>
                <div class="bg-muted/30 rounded-lg p-4">
                    <pre class="text-sm text-foreground overflow-x-auto"><code>{{ $log['raw'] }}</code></pre>
                </div>
            </div>

            <!-- Log Metadata -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-muted/30 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-muted-foreground mb-1">Índice</h4>
                    <p class="text-sm text-foreground font-mono">{{ $index }}</p>
                </div>
                <div class="bg-muted/30 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-muted-foreground mb-1">Data</h4>
                    <p class="text-sm text-foreground font-mono">{{ $date }}</p>
                </div>
                <div class="bg-muted/30 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-muted-foreground mb-1">Tipo</h4>
                    <p class="text-sm text-foreground font-mono">{{ ucfirst($type) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-sm text-muted-foreground">
            Log {{ $index + 1 }} de {{ $date }}
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.logs.show', ['date' => $date, 'index' => max(0, $index - 1), 'type' => $type]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
               @if($index <= 0) disabled @endif>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Anterior
            </a>
            <a href="{{ route('admin.logs.show', ['date' => $date, 'index' => $index + 1, 'type' => $type]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                Próximo
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
