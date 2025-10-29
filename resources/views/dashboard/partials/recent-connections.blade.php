@if($whatsappStatus && $whatsappStatus['success'])
    <!-- Status Card -->
    <div class="mb-4 p-3 rounded-lg {{ $whatsappStatus['data']['Connected'] ? 'bg-success/10' : 'bg-destructive/10' }}">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full {{ $whatsappStatus['data']['Connected'] ? 'bg-success' : 'bg-destructive' }}"></span>
            <span class="text-sm font-medium {{ $whatsappStatus['data']['Connected'] ? 'text-success' : 'text-destructive' }}">
                {{ $whatsappStatus['data']['Connected'] ? 'WhatsApp Conectado' : 'WhatsApp Desconectado' }}
            </span>
            @if($whatsappStatus['data']['LoggedIn'] ?? false)
                <span class="text-xs text-muted-foreground">• Autenticado</span>
            @endif
        </div>
    </div>
@endif

<div class="space-y-3">
    @forelse($recentConnections as $connection)
        <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-accent/50 transition-colors">
            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-foreground truncate">{{ $connection->phone_number }}</p>
                <p class="text-xs text-muted-foreground">
                    Instance: <code class="bg-muted px-1 rounded text-xs">{{ $connection->instance_id }}</code>
                </p>
                @if($connection->last_sync)
                    <p class="text-xs text-muted-foreground mt-0.5">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $connection->last_sync->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($connection->status === 'active' || $connection->status === 'connected') bg-success/10 text-success
                @elseif($connection->status === 'disconnected') bg-destructive/10 text-destructive
                @else bg-warning/10 text-warning @endif">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5
                    @if($connection->status === 'active' || $connection->status === 'connected') bg-success
                    @elseif($connection->status === 'disconnected') bg-destructive
                    @else bg-warning @endif">
                </span>
                {{ $connection->status === 'active' || $connection->status === 'connected' ? 'Conectado' : ($connection->status === 'disconnected' ? 'Desconectado' : ucfirst($connection->status)) }}
            </span>
        </div>
    @empty
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-foreground mb-1">Nenhuma conexão encontrada</p>
            <p class="text-xs text-muted-foreground mb-4">Conecte seu WhatsApp para começar</p>
            <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Conectar WhatsApp
            </a>
        </div>
    @endforelse
</div>

