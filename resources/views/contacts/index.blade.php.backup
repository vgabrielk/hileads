@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Contatos</h1>
            <p class="text-muted-foreground mt-1">Faça a gestão a sua lista de contatos</p>
        </div>
        <button onclick="location.reload()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Atualizar
        </button>
    </div>

    <!-- Error Alert -->
    @if(isset($error))
        <div class="relative rounded-lg border border-destructive/20 bg-destructive/10 p-4 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-destructive mb-1">Erro ao carregar dados</p>
                <p class="text-sm text-destructive opacity-90">{{ $error }}</p>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">Total de Grupos</p>
                    <p class="text-3xl font-bold text-foreground">{{ number_format($stats['groups']) }}</p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-success">↑ Via API Wuzapi</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">Total de Leads</p>
                    <p class="text-3xl font-bold text-foreground">{{ number_format($stats['total']) }}</p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-success">↑ Via API Wuzapi</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="relative">
        <form method="GET" action="{{ route('contacts.index') }}" class="flex gap-4">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Pesquisar contato..." 
                    class="w-full pl-10 pr-4 py-3 bg-card border border-input rounded-lg text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
            </div>
            <button type="submit" class="px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                Procurar
            </button>
            @if($search ?? false)
                <a href="{{ route('contacts.index') }}" class="px-6 py-3 text-sm font-medium text-foreground bg-secondary hover:bg-secondary/80 rounded-lg transition-colors">
                    Limpar
                </a>
            @endif
        </form>
    </div>

    <!-- Contacts Table -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-secondary border-b border-border">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Nome</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Telefone</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Grupo</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-accent/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-primary">
                                            {{ substr($contact['name'] ?: $contact['phone'], 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-foreground">{{ $contact['name'] ?: 'Sem nome' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-foreground">{{ $contact['phone'] }}</td>
                            <td class="px-6 py-4 text-foreground">{{ $contact['group_name'] ?? 'Grupo não encontrado' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($contact['found']) bg-success/10 text-success
                                    @else bg-muted text-muted-foreground @endif">
                                    {{ $contact['found'] ? 'Encontrado' : 'Não encontrado' }}
                                </span>
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
                                <p class="text-sm font-medium text-foreground mb-1">Nenhum contato encontrado</p>
                                <p class="text-xs text-muted-foreground">Nenhum contato encontrado na API</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($totalPages) && $totalPages > 1)
            <div class="px-6 py-4 border-t border-border flex items-center justify-between">
                <p class="text-sm text-muted-foreground">Mostrando {{ count($contacts) }} de {{ $totalContacts }} contatos</p>
                <div class="flex items-center gap-2">
                    @if($currentPage > 1)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}" 
                           class="px-3 py-2 text-sm font-medium text-muted-foreground bg-card border border-border rounded-lg hover:bg-accent transition-colors">
                            Anterior
                        </a>
                    @endif
                    
                    @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" 
                           class="px-3 py-2 text-sm font-medium {{ $i == $currentPage ? 'text-primary-foreground bg-primary border border-primary' : 'text-foreground bg-card border border-border hover:bg-accent' }} rounded-lg transition-colors">
                            {{ $i }}
                        </a>
                    @endfor
                    
                    @if($currentPage < $totalPages)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}" 
                           class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition-colors">
                            Próximo
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
