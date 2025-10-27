@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Conexões WhatsApp</h1>
                <p class="text-muted-foreground mt-1">Gerencie suas conexões WhatsApp</p>
            </div>
            <a href="{{ route('whatsapp.connect-flow') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors w-full sm:w-auto justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Conectar WhatsApp
            </a>
        </div>
    </div>

        <!-- Status Card -->
        @isset($status)
            <div class="mb-6">
                @if(!$status['success'])
                    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800">{{ $status['message'] ?? 'Não foi possível obter o status da conexão.' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Connected Status -->
                        <div class="bg-card rounded-lg border border-border p-4 sm:p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground mb-2">Status da Conexão</p>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                            @if($status['data']['Connected'] ?? false) bg-success/10 text-success
                                            @else bg-destructive/10 text-destructive @endif">
                                            <span class="w-2 h-2 rounded-full mr-2 @if($status['data']['Connected'] ?? false) bg-success @else bg-destructive @endif"></span>
                                            {{ ($status['data']['Connected'] ?? false) ? 'Conectado' : 'Desconectado' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Logged In Status -->
                        <div class="bg-card rounded-lg border border-border p-4 sm:p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground mb-2">Status de Login</p>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                            @if($status['data']['LoggedIn'] ?? false) bg-primary/10 text-primary
                                            @else bg-muted text-muted-foreground @endif">
                                            <span class="w-2 h-2 rounded-full mr-2 @if($status['data']['LoggedIn'] ?? false) bg-primary @else bg-muted-foreground @endif"></span>
                                            {{ ($status['data']['LoggedIn'] ?? false) ? 'Autenticado' : 'Não autenticado' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>
                @endif
            </div>
        @endisset

        <!-- QR Code Section -->
        @if(!empty($qrCode))
            <div class="mb-6">
                <div class="bg-card rounded-lg border border-border overflow-hidden">
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-success/10 rounded-lg mb-6 animate-pulse">
                            <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-foreground mb-2">Escaneie o QR Code</h3>
                        <p class="text-muted-foreground mb-8">Abra o WhatsApp no seu celular e escaneie o código abaixo</p>
                        <div class="inline-block p-6 bg-background rounded-lg border-2 border-border shadow-lg">
                            <img src="{{ $qrCode }}" alt="QR Code WhatsApp" class="w-64 h-64 mx-auto">
                        </div>
                        <div class="mt-8 flex items-center justify-center space-x-4">
                            <div class="flex items-center text-sm text-muted-foreground">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span>1. Abra o WhatsApp</span>
                            </div>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                                <span>2. Toque em Configurações</span>
                            </div>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                <span>3. Escaneie o QR Code</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Connections List -->
        @if($connections->count() > 0 || (isset($status) && $status['success'] && ($status['data']['Connected'] ?? false)))
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-foreground mb-4">Minhas Conexões</h2>
                
                @if($connections->count() > 0)
                    @foreach($connections as $connection)
                    <div class="bg-card rounded-lg border border-border overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <!-- Connection Info -->
                                <div class="flex items-center space-x-4 flex-1">
                                    <div class="w-14 h-14 bg-success/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-foreground">{{ $connection->phone_number }}</h3>
                                        <p class="text-sm text-muted-foreground mt-1">
                                            <span class="font-medium">Instance ID:</span> 
                                            <code class="bg-muted px-2 py-0.5 rounded text-xs">{{ $connection->instance_id }}</code>
                                        </p>
                                        <p class="text-xs text-muted-foreground mt-1">
                                            <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Última sincronização: {{ $connection->last_sync ? $connection->last_sync->format('d/m/Y H:i') : 'Nunca' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                    @if($connection->status === 'connected') bg-success/10 text-success
                                    @elseif($connection->status === 'disconnected') bg-destructive/10 text-destructive
                                    @else bg-warning/10 text-warning @endif">
                                    <span class="w-2 h-2 rounded-full mr-2
                                        @if($connection->status === 'connected') bg-success
                                        @elseif($connection->status === 'disconnected') bg-destructive
                                        @else bg-warning @endif">
                                    </span>
                                    {{ $connection->status === 'connected' ? 'Conectado' : ($connection->status === 'disconnected' ? 'Desconectado' : ucfirst($connection->status)) }}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="mt-6 flex flex-wrap items-center gap-3 pt-6 border-t border-border">
                                <a href="{{ route('whatsapp.show', $connection) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver Detalhes
                                </a>

                                <form method="POST" action="{{ route('whatsapp.destroy', $connection) }}" class="inline ml-auto" onsubmit="return handleDeleteConfirmation(event, 'Tem certeza que deseja remover esta conexão?', 'Esta ação não pode ser desfeita.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remover
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @elseif(isset($status) && $status['success'] && ($status['data']['Connected'] ?? false))
                    <!-- API Connection Status (when no DB connections exist) -->
                    <div class="bg-card rounded-lg border border-border overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <!-- Connection Info -->
                                <div class="flex items-center space-x-4 flex-1">
                                    <div class="w-14 h-14 bg-success/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-foreground">Conexão WhatsApp Ativa</h3>
                                        <p class="text-sm text-muted-foreground mt-1">
                                            <span class="font-medium">Status:</span> 
                                            <span class="text-success font-medium">Conectado via API</span>
                                        </p>
                                        <p class="text-xs text-muted-foreground mt-1">
                                            <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Última verificação: {{ now()->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-success/10 text-success">
                                    <span class="w-2 h-2 rounded-full mr-2 bg-success"></span>
                                    Conectado
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="mt-6 flex flex-wrap items-center gap-3 pt-6 border-t border-border">
                                <a href="{{ route('mass-sendings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Criar Campanha
                                </a>

                                <a href="{{ route('contacts.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-secondary-foreground bg-secondary hover:bg-secondary/90 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Ver Contatos
                                </a>

                                <!-- Disconnect Button -->
                                <form method="POST" action="{{ route('whatsapp.disconnect') }}" class="inline" onsubmit="return handleDisconnectConfirmation(event, 'Tem certeza que deseja desconectar?', 'A sessão será mantida e você poderá reconectar sem escanear o QR Code novamente.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-warning-foreground bg-warning hover:bg-warning/90 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                        </svg>
                                        Desconectar
                                    </button>
                                </form>

                                <!-- Logout Button -->
                                <form method="POST" action="{{ route('whatsapp.logout') }}" class="inline" onsubmit="return handleLogoutConfirmation(event, 'Tem certeza que deseja fazer logout?', 'Isso irá terminar a sessão e você precisará escanear o QR Code novamente para conectar.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-foreground mb-2">Nenhuma conexão WhatsApp encontrada</h3>
                <p class="text-muted-foreground mb-6 max-w-md mx-auto">
                    Conecte seu WhatsApp para começar a criar campanhas e gerenciar contatos
                </p>
                <a href="{{ route('whatsapp.connect-flow') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Conectar Primeiro WhatsApp
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

<script>
// Handle WhatsApp connection deletion confirmation
async function handleDeleteConfirmation(event, message, subtitle) {
    event.preventDefault();
    
    const confirmed = await confirmAction({
        type: 'danger',
        title: 'Remover Conexão',
        subtitle: subtitle,
        message: message,
        confirmText: 'Remover',
        cancelText: 'Cancelar'
    });
    
    if (confirmed) {
        event.target.submit();
    }
    
    return false;
}

// Handle WhatsApp disconnect confirmation
async function handleDisconnectConfirmation(event, message, subtitle) {
    event.preventDefault();
    
    const confirmed = await confirmAction({
        type: 'warning',
        title: 'Desconectar WhatsApp',
        subtitle: subtitle,
        message: message,
        confirmText: 'Desconectar',
        cancelText: 'Manter Conectado'
    });
    
    if (confirmed) {
        event.target.submit();
    }
    
    return false;
}

// Handle WhatsApp logout confirmation
async function handleLogoutConfirmation(event, message, subtitle) {
    event.preventDefault();
    
    const confirmed = await confirmAction({
        type: 'danger',
        title: 'Fazer Logout',
        subtitle: subtitle,
        message: message,
        confirmText: 'Fazer Logout',
        cancelText: 'Cancelar'
    });
    
    if (confirmed) {
        event.target.submit();
    }
    
    return false;
}
</script>
