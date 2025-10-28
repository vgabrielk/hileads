@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Gerir Campanhas</h1>
            <p class="text-muted-foreground mt-1">Visualize e faça a gestão todas as campanhas do sistema</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.campaigns.statistics') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Estatísticas
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Total</p>
                    <p class="text-2xl font-bold text-foreground">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Concluídas</p>
                    <p class="text-2xl font-bold text-success">{{ number_format($stats['completed']) }}</p>
                </div>
                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Falharam</p>
                    <p class="text-2xl font-bold text-destructive">{{ number_format($stats['failed']) }}</p>
                </div>
                <div class="w-12 h-12 bg-destructive/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Mensagens Enviadas</p>
                    <p class="text-2xl font-bold text-foreground">{{ number_format($stats['sent_messages']) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Todos os status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Utilizador</label>
                <select name="user_id" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Todos os utilizadores</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tipo de Mensagem</label>
                <select name="message_type" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Todos os tipos</option>
                    @foreach($messageTypes as $type)
                        <option value="{{ $type }}" {{ request('message_type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Procurar Utilizador</label>
                <input type="text" name="user_search" value="{{ request('user_search') }}" 
                       placeholder="Nome ou e-mail" 
                       class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Data Inicial</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Data Final</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div class="lg:col-span-6 flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrar
                </button>
                <a href="{{ route('admin.campaigns.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Utilizador</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Título</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Tipo</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Destinatários</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Enviadas</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Criado em</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($campaigns as $campaign)
                        <tr class="hover:bg-muted/30">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-foreground">{{ $campaign->user->name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ $campaign->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-foreground">{{ Str::limit($campaign->title, 30) }}</p>
                                    @if($campaign->message)
                                        <p class="text-sm text-muted-foreground">{{ Str::limit($campaign->message, 50) }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($campaign->status === 'completed') bg-success/10 text-success
                                    @elseif($campaign->status === 'sending') bg-warning/10 text-warning
                                    @elseif($campaign->status === 'pending') bg-primary/10 text-primary
                                    @elseif($campaign->status === 'failed') bg-destructive/10 text-destructive
                                    @elseif($campaign->status === 'cancelled') bg-muted/10 text-muted-foreground
                                    @else bg-muted/10 text-muted-foreground @endif">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary/10 text-primary">
                                    {{ ucfirst($campaign->message_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-foreground">{{ number_format($campaign->total_recipients) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-foreground">{{ number_format($campaign->sent_count) }}</p>
                                    @if($campaign->failed_count > 0)
                                        <p class="text-sm text-destructive">{{ number_format($campaign->failed_count) }} falharam</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-muted-foreground">{{ $campaign->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.campaigns.show', $campaign) }}" 
                                       class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-primary hover:bg-primary/10 rounded transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.campaigns.edit', $campaign) }}" 
                                       class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-warning hover:bg-warning/10 rounded transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-muted-foreground">
                                Nenhuma campanha encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($campaigns->hasPages())
            <div class="px-4 py-3 border-t border-border">
                {{ $campaigns->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
