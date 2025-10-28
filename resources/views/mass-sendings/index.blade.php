@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
            <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Campanhas</h1>
        <p class="text-muted-foreground mt-1">Faça a gestão e monitorize as suas campanhas de marketing</p>
    </div>

    <!-- Filters and Search -->
    <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left side - Filters and Search -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                <!-- Category Dropdown -->
                <div class="relative flex-1 sm:flex-none sm:w-48">
                    <select class="w-full appearance-none bg-background border border-input rounded-xl px-4 py-3 pr-10 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                        <option value="">Todas as categorias</option>
                        <option value="draft">Rascunho</option>
                        <option value="active">Ativa</option>
                        <option value="paused">Pausada</option>
                        <option value="completed">Concluída</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Search Input -->
                <div class="relative flex-1 sm:flex-none sm:w-80">
                    <input type="text" placeholder="Procurar campanhas..." class="w-full pl-12 pr-4 py-3 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Add Button and View Toggle -->
            <div class="flex items-center gap-4">
                <!-- Add Mass Sending Button -->
                <a href="{{ route('mass-sendings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 transition-all hover:shadow-md w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Nova Campanha</span>
                </a>
                
                <!-- View Toggle Buttons -->
                <div class="hidden sm:flex items-center bg-muted rounded-lg p-1 gap-1">
                    <button class="p-2 text-primary bg-background rounded-md shadow-sm transition-all hover:shadow-md" title="Vista de lista">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </button>
                    <button class="p-2 text-muted-foreground hover:text-foreground transition-colors rounded-md hover:bg-background/50" title="Vista de grelha">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm">
        <!-- Mobile Table Container with Horizontal Scroll -->
        <div class="overflow-x-auto">
        <!-- Table Header with Sortable Columns -->
        <div class="px-4 sm:px-6 py-5 border-b border-border bg-muted/30">
            <div class="grid grid-cols-9 gap-3 sm:gap-6 items-center min-w-[900px]">
                <div class="flex items-center">
                    <input type="checkbox" class="w-4 h-4 rounded border-input text-primary focus:ring-primary focus:ring-offset-0">
                </div>
                <div class="flex items-center gap-2 cursor-pointer hover:text-primary transition-colors">
                    <span class="text-sm font-semibold text-foreground uppercase tracking-wide">Nome/ID</span>
                    <svg class="w-3 h-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 cursor-pointer hover:text-primary transition-colors">
                    <span class="text-sm font-semibold text-foreground uppercase tracking-wide">Mensagem</span>
                    <svg class="w-3 h-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 cursor-pointer hover:text-primary transition-colors">
                    <span class="text-sm font-semibold text-foreground uppercase tracking-wide">Total</span>
                    <svg class="w-3 h-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 cursor-pointer hover:text-primary transition-colors">
                    <span class="text-sm font-semibold text-foreground uppercase tracking-wide">Enviados</span>
                    <svg class="w-3 h-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 cursor-pointer hover:text-primary transition-colors">
                    <span class="text-sm font-semibold text-foreground uppercase tracking-wide">Entregues</span>
                    <svg class="w-3 h-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 cursor-pointer hover:text-primary transition-colors">
                    <span class="text-sm font-semibold text-foreground uppercase tracking-wide">Criado em</span>
                    <svg class="w-3 h-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2 cursor-pointer hover:text-primary transition-colors">
                    <span class="text-sm font-semibold text-foreground uppercase tracking-wide">Status</span>
                    <svg class="w-3 h-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-foreground uppercase tracking-wide">Ações</span>
                </div>
            </div>
        </div>

        <!-- Filter Row -->
        <div class="px-4 sm:px-6 py-4 border-b border-border bg-muted/20">
            <div class="grid grid-cols-9 gap-3 sm:gap-6 items-center min-w-[900px]">
                <div class="flex items-center">
                    <input type="checkbox" class="w-4 h-4 rounded border-input text-primary focus:ring-primary focus:ring-offset-0">
                </div>
                <div class="relative">
                    <input type="text" placeholder="Procurar..." class="w-full pl-8 pr-3 py-2.5 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <input type="text" placeholder="Mensagem..." class="w-full pr-8 pl-3 py-2.5 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <input type="text" placeholder="Total..." class="w-full pl-8 pr-3 py-2.5 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <input type="text" placeholder="Enviados..." class="w-full pr-8 pl-3 py-2.5 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <input type="text" placeholder="Entregues..." class="w-full pl-8 pr-3 py-2.5 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <input type="text" placeholder="Criado em..." class="w-full pr-8 pl-3 py-2.5 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <input type="text" placeholder="Status..." class="w-full pr-8 pl-3 py-2.5 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <div></div>
            </div>
        </div>

        <!-- Data Rows -->
        @if($massSendings->count() > 0)
            @foreach($massSendings as $index => $massSending)
                <div class="px-4 sm:px-6 py-5 border-b border-border hover:bg-accent/30 transition-all duration-200 {{ $index === 0 ? 'bg-accent/20' : '' }}" data-mass-sending-id="{{ $massSending->id }}">
                    <div class="grid grid-cols-9 gap-3 sm:gap-6 items-center min-w-[900px]">
                        <!-- Checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 rounded border-input text-primary focus:ring-primary focus:ring-offset-0" {{ $index === 0 ? 'checked' : '' }}>
                        </div>
                        
                        <!-- Nome/ID -->
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-primary hover:text-primary/80 cursor-pointer transition-colors">{{ $massSending->name }}</span>
                            <span class="text-xs text-muted-foreground font-mono">ID: {{ $massSending->id }}</span>
                        </div>
                        
                        <!-- Mensagem -->
                        <div class="flex flex-col">
                            <span class="text-sm text-foreground font-medium">{{ Str::limit($massSending->message, 25) ?: 'Sem mensagem' }}</span>
                            <span class="text-xs text-muted-foreground bg-muted px-2 py-0.5 rounded-full inline-block w-fit">{{ $massSending->message_type ?? 'texto' }}</span>
                        </div>
                        
                        <!-- Total -->
                        <div class="text-sm font-semibold text-foreground">{{ number_format($massSending->total_contacts ?? 0) }}</div>
                        
                        <!-- Enviados -->
                        <div class="text-sm font-semibold text-foreground">{{ number_format($massSending->sent_count ?? 0) }}</div>
                        
                        <!-- Entregues -->
                        <div class="text-sm font-semibold text-foreground">{{ number_format($massSending->delivered_count ?? 0) }}</div>
                        
                        <!-- Criado em -->
                        <div class="text-sm text-foreground font-medium">{{ $massSending->created_at->format('d/m/Y') }}</div>
                        
                        <!-- Status -->
                        <div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($massSending->status === 'draft') bg-yellow-100 text-yellow-800
                                @elseif($massSending->status === 'active') bg-green-100 text-green-800
                                @elseif($massSending->status === 'paused') bg-orange-100 text-orange-800
                                @elseif($massSending->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($massSending->status === 'draft') Rascunho
                                @elseif($massSending->status === 'active') Ativa
                                @elseif($massSending->status === 'paused') Pausada
                                @elseif($massSending->status === 'completed') Concluída
                                @else {{ ucfirst($massSending->status) }}
                                @endif
                            </span>
                        </div>
                        
                        <!-- Ações -->
                        <div class="flex items-center gap-1.5">
                                <!-- View Details Button -->
                                <a href="{{ route('mass-sendings.show', $massSending) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-md transition-all group"
                                   title="Ver detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <!-- Edit Button -->
                                <a href="{{ route('mass-sendings.edit', $massSending) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-muted-foreground hover:text-warning hover:bg-warning/10 rounded-md transition-all group"
                                   title="Editar campanha">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <!-- Actions Dropdown -->
                                <div class="relative group">
                                    <button class="inline-flex items-center justify-center w-8 h-8 text-muted-foreground hover:text-foreground hover:bg-muted rounded-md transition-all"
                                            title="Mais ações">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div class="absolute right-0 top-full mt-1 w-48 bg-card border border-border rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                        <div class="py-1">
                                            @if($massSending->status === 'draft')
                                                <form method="POST" action="{{ route('mass-sendings.start', $massSending) }}" class="block">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-accent flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Iniciar Campanha
                                                    </button>
                                                </form>
                                            @elseif($massSending->status === 'active')
                                                <form method="POST" action="{{ route('mass-sendings.pause', $massSending) }}" class="block">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-accent flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6l4-3-4-3z"></path>
                                                        </svg>
                                                        Pausar Campanha
                                                    </button>
                                                </form>
                                            @elseif($massSending->status === 'paused')
                                                <form method="POST" action="{{ route('mass-sendings.resume', $massSending) }}" class="block">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-accent flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Retomar Campanha
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <div class="border-t border-border my-1"></div>
                                            
                                            <form method="POST" action="{{ route('mass-sendings.destroy', $massSending) }}" 
                                                  onsubmit="return handleDeleteConfirmation(event, 'Tem certeza que deseja eliminar esta campanha?', 'Esta ação não pode ser desfeita.')" 
                                                  class="block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Eliminar Campanha
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-foreground mb-2">Nenhuma campanha encontrada</h3>
                <p class="text-muted-foreground mb-6 max-w-md mx-auto">
                    Crie a sua primeira campanha de marketing para começar a enviar mensagens aos seus leads
                </p>
                <a href="{{ route('mass-sendings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Criar Primeira Campanha
                </a>
            </div>
        @endif
        </div> <!-- End overflow-x-auto -->
    </div>

        <!-- Pagination -->
        @if($massSendings->hasPages())
        <div class="px-3 sm:px-6 py-4 border-t border-border bg-card">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-muted-foreground text-center sm:text-left">Mostrando {{ $massSendings->firstItem() }} a {{ $massSendings->lastItem() }} de {{ $massSendings->total() }} campanhas</p>
                <div class="flex items-center gap-2 flex-wrap justify-center sm:justify-end">
                    @if($massSendings->onFirstPage())
                        <button class="px-3 py-2 text-sm font-medium text-muted-foreground bg-secondary border border-border rounded-lg cursor-not-allowed opacity-50">
                            Anterior
                        </button>
                    @else
                        <a href="{{ $massSendings->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition-colors">
                            Anterior
                        </a>
        @endif
                    
                    @foreach($massSendings->getUrlRange(1, $massSendings->lastPage()) as $page => $url)
                        @if($page == $massSendings->currentPage())
                            <button class="px-3 py-2 text-sm font-medium text-primary-foreground bg-primary border border-primary rounded-lg">
                                {{ $page }}
                            </button>
                        @else
                            <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                    
                    @if($massSendings->hasMorePages())
                        <a href="{{ $massSendings->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition-colors">
                            Próximo
                        </a>
                    @else
                        <button class="px-3 py-2 text-sm font-medium text-muted-foreground bg-secondary border border-border rounded-lg cursor-not-allowed opacity-50">
                            Próximo
                </button>
                    @endif
                </div>
                </div>
        </div>
    @endif
</div>

<script>
// Handle delete confirmation with modal
async function handleDeleteConfirmation(event, message, subtitle) {
    event.preventDefault();
    
    const confirmed = await confirmAction({
        type: 'danger',
        title: 'Eliminar Campanha',
        subtitle: subtitle,
        message: message,
        confirmText: 'Eliminar',
        cancelText: 'Cancelar'
    });
    
    if (confirmed) {
        // Submit the form
        event.target.submit();
    }
    
    return false; // Prevent default form submission
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add search functionality to filter inputs
    const searchInputs = document.querySelectorAll('input[placeholder*="Search"], input[placeholder*="Mensagem"], input[placeholder*="Total"], input[placeholder*="Enviados"], input[placeholder*="Entregues"], input[placeholder*="Criado em"], input[placeholder*="Status"]');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('[data-mass-sending-id]');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Category filter functionality
    const categorySelect = document.querySelector('select');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            const rows = document.querySelectorAll('[data-mass-sending-id]');
            
            rows.forEach(row => {
                if (selectedCategory === '' || row.textContent.toLowerCase().includes(selectedCategory.toLowerCase())) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection
