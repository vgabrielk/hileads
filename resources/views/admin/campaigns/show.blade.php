@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Detalhes da Campanha</h1>
            <p class="text-muted-foreground mt-1">Visualize e faça a gestão esta campanha</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.campaigns.edit', $campaign) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-warning-foreground bg-warning hover:bg-warning/90 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="{{ route('admin.campaigns.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Campaign Details -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID da Campanha</label>
                        <p class="text-foreground font-mono text-sm">{{ $campaign->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($campaign->status === 'completed') bg-success/10 text-success
                            @elseif($campaign->status === 'sending') bg-warning/10 text-warning
                            @elseif($campaign->status === 'pending') bg-primary/10 text-primary
                            @elseif($campaign->status === 'failed') bg-destructive/10 text-destructive
                            @elseif($campaign->status === 'cancelled') bg-muted/10 text-muted-foreground
                            @else bg-muted/10 text-muted-foreground @endif">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Título</label>
                        <p class="text-foreground">{{ $campaign->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Tipo de Mensagem</label>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary/10 text-primary">
                            {{ ucfirst($campaign->message_type) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Total de Destinatários</label>
                        <p class="text-foreground font-semibold">{{ number_format($campaign->total_recipients) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Mensagens Enviadas</label>
                        <p class="text-foreground font-semibold">{{ number_format($campaign->sent_count) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Mensagens Falharam</label>
                        <p class="text-foreground font-semibold text-destructive">{{ number_format($campaign->failed_count) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Criado em</label>
                        <p class="text-foreground">{{ $campaign->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($campaign->started_at)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Iniciado em</label>
                        <p class="text-foreground">{{ $campaign->started_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @if($campaign->completed_at)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Concluído em</label>
                        <p class="text-foreground">{{ $campaign->completed_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @if($campaign->cancelled_at)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Cancelado em</label>
                        <p class="text-foreground">{{ $campaign->cancelled_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Message Content -->
            @if($campaign->message)
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Conteúdo da Mensagem</h3>
                <div class="bg-muted/30 rounded-lg p-4">
                    <p class="text-foreground whitespace-pre-wrap">{{ $campaign->message }}</p>
                </div>
            </div>
            @endif

            <!-- Media Files -->
            @if($campaign->media_files && count($campaign->media_files) > 0)
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ficheiros de Mídia</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($campaign->media_files as $file)
                        <div class="border border-border rounded-lg p-3">
                            <p class="text-sm font-medium text-foreground">{{ basename($file) }}</p>
                            <p class="text-xs text-muted-foreground">{{ $file }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- User Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações do Usuário</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Nome</label>
                        <p class="text-foreground">{{ $campaign->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">E-mail</label>
                        <p class="text-foreground">{{ $campaign->user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID do Usuário</label>
                        <p class="text-foreground font-mono text-sm">{{ $campaign->user->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Tipo de Usuário</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $campaign->user->isAdmin() ? 'bg-primary/10 text-primary' : 'bg-muted/10 text-muted-foreground' }}">
                            {{ $campaign->user->isAdmin() ? 'Administrador' : 'Usuário' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Connection Info -->
            @if($campaign->whatsappConnection)
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Conexão WhatsApp</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID da Conexão</label>
                        <p class="text-foreground font-mono text-sm">{{ $campaign->whatsappConnection->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $campaign->whatsappConnection->status === 'connected' ? 'bg-success/10 text-success' : 'bg-destructive/10 text-destructive' }}">
                            {{ ucfirst($campaign->whatsappConnection->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($campaign->notes)
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Observações</h3>
                <p class="text-foreground whitespace-pre-wrap">{{ $campaign->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Quick Actions -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ações Rápidas</h3>
                <div class="space-y-2">
                    @if($campaign->status === 'pending' || $campaign->status === 'sending')
                        <form method="POST" action="{{ route('admin.campaigns.cancel', $campaign) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Tem certeza que deseja cancelar esta campanha?')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar Campanha
                            </button>
                        </form>
                    @elseif($campaign->status === 'failed')
                        <form method="POST" action="{{ route('admin.campaigns.restart', $campaign) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Tem certeza que deseja reiniciar esta campanha?')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-success-foreground bg-success hover:bg-success/90 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reiniciar Campanha
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" class="inline-block w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Tem certeza que deseja excluir esta campanha? Esta ação não pode ser desfeita.')"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Excluir Campanha
                        </button>
                    </form>
                </div>
            </div>

            <!-- Progress Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Progresso</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-muted-foreground">Enviadas</span>
                            <span class="text-foreground">{{ number_format($campaign->sent_count) }} / {{ number_format($campaign->total_recipients) }}</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $campaign->total_recipients > 0 ? ($campaign->sent_count / $campaign->total_recipients) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @if($campaign->failed_count > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-muted-foreground">Falharam</span>
                            <span class="text-destructive">{{ number_format($campaign->failed_count) }}</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-destructive h-2 rounded-full" style="width: {{ $campaign->total_recipients > 0 ? ($campaign->failed_count / $campaign->total_recipients) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
