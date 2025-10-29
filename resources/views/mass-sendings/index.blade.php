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
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 flex-1 min-w-0">
                <!-- Category Dropdown -->
                <div class="relative flex-shrink-0 w-full sm:w-48">
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
                <div class="relative flex-1 min-w-0 sm:w-64 lg:w-80">
                    <input type="text" placeholder="Procurar campanhas..." class="w-full pl-12 pr-4 py-3 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Add Button and View Toggle -->
            <div class="flex items-center gap-4 flex-shrink-0">
                <!-- Add Mass Sending Button -->
                <a href="{{ route('mass-sendings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 transition-all hover:shadow-md whitespace-nowrap justify-center">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Nova Campanha</span>
                </a>
                
                <!-- View Toggle Buttons -->
                <div class="hidden sm:flex items-center bg-muted rounded-lg p-1 gap-1 flex-shrink-0">
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

    <!-- Campaign List - Consistent View for All Resolutions -->
    <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-border bg-muted/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-base font-semibold text-foreground">Campanhas</h2>
                    <p class="text-sm text-muted-foreground">Gerencie suas campanhas de marketing ativas e concluídas</p>
                </div>
            </div>
        </div>

        <!-- Campaign List Items -->
        <div class="divide-y divide-border">
            @if($massSendings->count() > 0)
                @foreach($massSendings as $massSending)
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-accent/50 transition-all duration-200 group relative"
                         data-mass-sending-id="{{ $massSending->id }}">
                        <!-- Status Indicator Bar -->
                        <div class="w-1 h-16 rounded-full flex-shrink-0
                            @if($massSending->status === 'draft') bg-yellow-500
                            @elseif($massSending->status === 'active') bg-green-500
                            @elseif($massSending->status === 'paused') bg-orange-500
                            @elseif($massSending->status === 'completed') bg-blue-500
                            @else bg-gray-400
                            @endif"></div>
                        
                        <!-- Campaign Info (Clickable Area) -->
                        <a href="{{ route('mass-sendings.show', $massSending) }}" class="flex-1 min-w-0 cursor-pointer">
                            <div class="flex items-start justify-between gap-4 mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-foreground group-hover:text-primary transition-colors truncate">
                                        {{ $massSending->name }}
                                    </h3>
                                    <p class="text-xs text-muted-foreground font-mono mt-0.5">ID: {{ $massSending->id }}</p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold flex-shrink-0
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
                            
                            <p class="text-sm text-muted-foreground mb-3 line-clamp-1">
                                {{ Str::limit($massSending->message, 100) ?: 'Sem mensagem' }}
                                <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs bg-muted text-muted-foreground">
                                    {{ $massSending->message_type ?? 'texto' }}
                                </span>
                            </p>
                            
                            <div class="flex items-center gap-4 text-xs text-muted-foreground flex-wrap">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="font-medium text-foreground">{{ number_format($massSending->total_contacts ?? 0) }}</span> total
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <span class="font-medium text-foreground">{{ number_format($massSending->sent_count ?? 0) }}</span> enviados
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium text-foreground">{{ number_format($massSending->delivered_count ?? 0) }}</span> entregues
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $massSending->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </a>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <!-- Edit Button -->
                            <a href="{{ route('mass-sendings.edit', $massSending) }}" 
                               class="inline-flex items-center justify-center w-9 h-9 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                               title="Editar campanha"
                               onclick="event.stopPropagation()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            
                            <!-- More Actions Dropdown -->
                            <div class="relative dropdown-container">
                                <button class="inline-flex items-center justify-center w-9 h-9 text-muted-foreground hover:text-foreground hover:bg-muted rounded-lg transition-all dropdown-trigger"
                                        title="Mais ações"
                                        onclick="event.stopPropagation(); toggleDropdown(this)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div class="absolute right-0 top-full mt-1 w-48 bg-card border border-border rounded-lg shadow-lg opacity-0 invisible dropdown-menu transition-all duration-200 z-10">
                                    <div class="py-1">
                                        @if($massSending->status === 'draft')
                                            <form method="POST" action="{{ route('mass-sendings.start', $massSending) }}" class="block" onclick="event.stopPropagation()">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-accent flex items-center gap-2 transition-colors">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Iniciar Campanha
                                                </button>
                                            </form>
                                        @elseif($massSending->status === 'active')
                                            <form method="POST" action="{{ route('mass-sendings.pause', $massSending) }}" class="block" onclick="event.stopPropagation()">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-accent flex items-center gap-2 transition-colors">
                                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Pausar Campanha
                                                </button>
                                            </form>
                                        @elseif($massSending->status === 'paused')
                                            <form method="POST" action="{{ route('mass-sendings.resume', $massSending) }}" class="block" onclick="event.stopPropagation()">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-accent flex items-center gap-2 transition-colors">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Retomar Campanha
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <div class="border-t border-border my-1"></div>
                                        
                                        <form method="POST" action="{{ route('mass-sendings.destroy', $massSending) }}" 
                                              onsubmit="return handleDeleteConfirmation(event, 'Tem certeza que deseja excluir esta campanha?', 'Esta ação não pode ser desfeita.')" 
                                              class="block"
                                              onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Excluir Campanha
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Arrow Icon -->
                            <a href="{{ route('mass-sendings.show', $massSending) }}" class="flex-shrink-0">
                                <svg class="w-5 h-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-16 px-6">
                    <div class="w-16 h-16 bg-muted rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-foreground mb-2">Nenhuma campanha encontrada</h3>
                    <p class="text-sm text-muted-foreground mb-6 max-w-sm mx-auto">
                        Crie a sua primeira campanha de marketing para começar a enviar mensagens aos seus leads
                    </p>
                    <a href="{{ route('mass-sendings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Criar Primeira Campanha
                    </a>
                </div>
            @endif
        </div>
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
// Toggle dropdown menu
function toggleDropdown(button) {
    const container = button.closest('.dropdown-container');
    const menu = container.querySelector('.dropdown-menu');
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(m => {
        if (m !== menu) {
            m.classList.remove('opacity-100', 'visible');
            m.classList.add('opacity-0', 'invisible');
        }
    });
    
    // Toggle current dropdown
    menu.classList.toggle('opacity-0');
    menu.classList.toggle('invisible');
    menu.classList.toggle('opacity-100');
    menu.classList.toggle('visible');
}

// Handle delete confirmation with modal
async function handleDeleteConfirmation(event, message, subtitle) {
    event.preventDefault();
    
    // Check if confirmAction function exists (from your modal system)
    if (typeof confirmAction === 'function') {
        const confirmed = await confirmAction({
            type: 'danger',
            title: 'Excluir Campanha',
            subtitle: subtitle,
            message: message,
            confirmText: 'Excluir',
            cancelText: 'Cancelar'
        });
        
        if (confirmed) {
            event.target.submit();
        }
    } else {
        // Fallback to native confirm
        if (confirm(message + '\n' + subtitle)) {
            event.target.submit();
        }
    }
    
    return false;
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Main search input
    const mainSearchInput = document.querySelector('input[placeholder*="Procurar campanhas"]');
    
    if (mainSearchInput) {
        mainSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const campaignItems = document.querySelectorAll('[data-mass-sending-id]');
            
            campaignItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Category filter functionality
    const categorySelect = document.querySelector('select');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            const campaignItems = document.querySelectorAll('[data-mass-sending-id]');
            
            campaignItems.forEach(item => {
                if (selectedCategory === '' || item.textContent.toLowerCase().includes(selectedCategory.toLowerCase())) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('opacity-100', 'visible');
                menu.classList.add('opacity-0', 'invisible');
            });
        }
    });
});
</script>
@endsection
