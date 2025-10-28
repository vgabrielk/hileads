@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-foreground">Dashboard</h1>
        <p class="text-muted-foreground mt-1">Visão geral do sistema</p>
    </div>

    <!-- Access Status Card -->
    @if($accessStatus['is_admin'])
        <div class="relative rounded-lg border border-primary/20 bg-primary/10 p-4 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-primary mb-1">Acesso Administrativo</p>
                <p class="text-sm text-primary opacity-90">Tem acesso completo a todas as funcionalidades do sistema.</p>
            </div>
        </div>
    @elseif($accessStatus['has_subscription'] && $accessStatus['current_plan'])
        <div class="relative rounded-lg border border-success/20 bg-success/10 p-4 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-success mb-1">Subscrição Ativa</p>
                <p class="text-sm text-success opacity-90">Plano: {{ $accessStatus['current_plan']->name }} - {{ $accessStatus['current_plan']->formatted_price }}</p>
            </div>
            <a href="{{ route('subscriptions.index') }}" class="text-success hover:opacity-70 transition-opacity">
                Gerir →
            </a>
        </div>
    @else
        <div class="relative rounded-lg border border-warning/20 bg-warning/10 p-4 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-warning mb-1">Subscrição Necessária</p>
                <p class="text-sm text-warning opacity-90">Precisa de uma subscrição ativa para aceder a todas as funcionalidades.</p>
            </div>
            <a href="{{ route('plans.index') }}" class="px-4 py-2 text-sm font-medium text-warning-foreground bg-warning hover:bg-warning/90 rounded-lg transition-colors">
                Ver Planos
            </a>
        </div>
    @endif

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card: Ligações -->
        <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">Ligações Ativas</p>
                    <p class="text-3xl font-bold text-foreground">{{ $stats['connections'] }}</p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-success">↑ WhatsApp ligados</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card: Grupos -->
        <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">Grupos Sincronizados</p>
                    <p class="text-3xl font-bold text-foreground">{{ $stats['groups'] }}</p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-success">↑ Fontes de leads</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card: Contactos -->
        <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">Total de Contactos</p>
                    <p class="text-3xl font-bold text-foreground">{{ number_format($stats['contacts'], 0, ',', '.') }}</p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-success">↑ Leads capturados</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card: Campanhas -->
        <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">Campanhas</p>
                    <p class="text-3xl font-bold text-foreground">{{ $stats['mass-sendings'] }}</p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-warning">↑ Em andamento</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Connections -->
        <div class="bg-card rounded-lg border border-border p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Ligações Recentes</h3>
            <div class="space-y-3">
                @forelse($recentConnections as $connection)
                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-accent/50 transition-colors">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-foreground truncate">{{ $connection->phone_number }}</p>
                            <p class="text-xs text-muted-foreground">Instance: {{ $connection->instance_id }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($connection->status === 'connected') bg-success/10 text-success
                            @elseif($connection->status === 'disconnected') bg-destructive/10 text-destructive
                            @else bg-warning/10 text-warning @endif">
                            {{ $connection->status === 'connected' ? 'Ligado' : ($connection->status === 'disconnected' ? 'Desconectado' : 'Pendente') }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-foreground mb-1">Nenhuma ligação encontrada</p>
                        <p class="text-xs text-muted-foreground mb-4">Ligue o seu WhatsApp para começar</p>
                        <a href="{{ route('whatsapp.connect') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Ligar WhatsApp
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Groups -->
        <div class="bg-card rounded-lg border border-border p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Grupos Recentes</h3>
            <div class="space-y-3">
                @forelse($recentGroups as $group)
                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-accent/50 transition-colors">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-foreground truncate">{{ $group->group_name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $group->participants_count }} participantes</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($group->is_extracted) bg-success/10 text-success
                            @else bg-warning/10 text-warning @endif">
                            @if($group->is_extracted)
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Extraído
                            @else
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Pendente
                            @endif
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-foreground mb-1">Nenhum grupo encontrado</p>
                        <p class="text-xs text-muted-foreground">Sincronize grupos do WhatsApp</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Contactos Recentes</h2>
                    <p class="text-sm text-muted-foreground mt-1">Últimos leads capturados</p>
                </div>
                <a href="{{ route('contacts.index') }}" class="text-primary hover:text-primary/80 text-sm font-medium flex items-center gap-1">
                    Ver todos
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-secondary border-b border-border">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Contacto</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Grupo</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Estado</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-foreground">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($recentContacts as $contact)
                        <tr class="hover:bg-accent/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-primary">
                                            {{ substr($contact->contact_name ?: $contact->phone_number, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-foreground">{{ $contact->contact_name ?: 'Sem nome' }}</p>
                                        <p class="text-sm text-muted-foreground">{{ $contact->phone_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-foreground">{{ $contact->whatsappGroup->group_name }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($contact->status === 'new') bg-primary/10 text-primary
                                    @elseif($contact->status === 'contacted') bg-warning/10 text-warning
                                    @elseif($contact->status === 'interested') bg-success/10 text-success
                                    @elseif($contact->status === 'not_interested') bg-destructive/10 text-destructive
                                    @else bg-muted text-muted-foreground @endif">
                                    @if($contact->status === 'new') Novo
                                    @elseif($contact->status === 'contacted') Contatado
                                    @elseif($contact->status === 'interested') Interessado
                                    @elseif($contact->status === 'not_interested') Não interessado
                                    @else {{ ucfirst(str_replace('_', ' ', $contact->status)) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-muted-foreground text-sm">Via API</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-foreground mb-1">Nenhum contacto encontrado</p>
                                <p class="text-xs text-muted-foreground">Extraia contactos dos grupos para começar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
