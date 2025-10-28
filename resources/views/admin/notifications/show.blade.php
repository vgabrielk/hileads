@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Detalhes da Notificação</h1>
            <p class="text-muted-foreground mt-1">Visualize e faça a gestão esta notificação</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.notifications.edit', $notification) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-warning-foreground bg-warning hover:bg-warning/90 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="{{ route('admin.notifications.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Notification Details -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID da Notificação</label>
                        <p class="text-foreground font-mono text-sm">{{ $notification->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Tipo</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($notification->type === 'error') bg-destructive/10 text-destructive
                            @elseif($notification->type === 'warning') bg-warning/10 text-warning
                            @elseif($notification->type === 'success') bg-success/10 text-success
                            @elseif($notification->type === 'info') bg-primary/10 text-primary
                            @else bg-muted/10 text-muted-foreground @endif">
                            {{ ucfirst($notification->type) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($notification->status === 'sent') bg-success/10 text-success
                            @elseif($notification->status === 'pending') bg-warning/10 text-warning
                            @elseif($notification->status === 'failed') bg-destructive/10 text-destructive
                            @elseif($notification->status === 'scheduled') bg-primary/10 text-primary
                            @else bg-muted/10 text-muted-foreground @endif">
                            {{ ucfirst($notification->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Criado em</label>
                        <p class="text-foreground">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($notification->scheduled_at)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Agendado para</label>
                        <p class="text-foreground">{{ $notification->scheduled_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @if($notification->sent_at)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Enviado em</label>
                        <p class="text-foreground">{{ $notification->sent_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @if($notification->read_at)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Lido em</label>
                        <p class="text-foreground">{{ $notification->read_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Conteúdo</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-2">Título</label>
                        <p class="text-foreground text-lg font-medium">{{ $notification->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-2">Mensagem</label>
                        <div class="bg-muted/30 rounded-lg p-4">
                            <p class="text-foreground whitespace-pre-wrap">{{ $notification->message }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Channels -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Canais de Envio</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($notification->channels as $channel)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary">
                            {{ ucfirst($channel) }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- User Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Destinatário</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Nome</label>
                        <p class="text-foreground">{{ $notification->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">E-mail</label>
                        <p class="text-foreground">{{ $notification->user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID do Utilizador</label>
                        <p class="text-foreground font-mono text-sm">{{ $notification->user->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Status do Utilizador</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $notification->user->is_active ? 'bg-success/10 text-success' : 'bg-destructive/10 text-destructive' }}">
                            {{ $notification->user->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Quick Actions -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ações Rápidas</h3>
                <div class="space-y-2">
                    @if($notification->status === 'pending' || $notification->status === 'scheduled')
                        <form method="POST" action="{{ route('admin.notifications.send', $notification) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-success-foreground bg-success hover:bg-success/90 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Enviar Agora
                            </button>
                        </form>
                    @endif

                    @if($notification->status === 'scheduled')
                        <form method="POST" action="{{ route('admin.notifications.cancel', $notification) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Tem certeza que deseja cancelar esta notificação?')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}" class="inline-block w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Tem certeza que deseja eliminar esta notificação? Esta ação não pode ser desfeita.')"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Status</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Criado:</span>
                        <span class="text-foreground">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($notification->scheduled_at)
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Agendado:</span>
                        <span class="text-foreground">{{ $notification->scheduled_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @if($notification->sent_at)
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Enviado:</span>
                        <span class="text-foreground">{{ $notification->sent_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @if($notification->read_at)
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Lido:</span>
                        <span class="text-foreground">{{ $notification->read_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Help -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ajuda</h3>
                <div class="space-y-2 text-sm text-muted-foreground">
                    <p><strong>Enviar:</strong> Envia a notificação imediatamente.</p>
                    <p><strong>Cancelar:</strong> Cancela notificações agendadas.</p>
                    <p><strong>Editar:</strong> Modifica o conteúdo da notificação.</p>
                    <p><strong>Eliminar:</strong> Remove a notificação permanentemente.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
