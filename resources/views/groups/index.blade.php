@extends('layouts.app')

@section('title', 'Grupos')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Grupos</h1>
        <p class="text-muted-foreground mt-1">Organize seus contactos em grupos</p>
    </div>

    <!-- Filters and Search -->
    <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left side - Search -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 flex-1 min-w-0">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-0 sm:w-64 lg:w-80">
                    <input type="text" placeholder="Procurar grupos..." class="w-full pl-12 pr-4 py-3 bg-background border border-input rounded-xl text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all hover:border-primary/50" id="searchGroupInput">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Add Button -->
            <div class="flex items-center gap-4 flex-shrink-0">
                <!-- Add Group Button -->
                <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 transition-all hover:shadow-md whitespace-nowrap justify-center">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Novo Grupo</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Groups List - Consistent View for All Resolutions -->
    <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-border bg-muted/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-base font-semibold text-foreground">Grupos</h2>
                    <p class="text-sm text-muted-foreground">Gerencie seus grupos de contactos</p>
                </div>
            </div>
        </div>

        <!-- Group List Items -->
        <div class="divide-y divide-border">
            @if($groups->count() > 0)
                @foreach($groups as $group)
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-accent/50 transition-all duration-200 group relative"
                         data-group-id="{{ $group->id }}">
                        <!-- Status Indicator Bar (color based on number of members) -->
                        <div class="w-1 h-16 rounded-full flex-shrink-0
                            @if($group->contacts_count === 0) bg-gray-400
                            @elseif($group->contacts_count < 10) bg-blue-500
                            @elseif($group->contacts_count < 50) bg-green-500
                            @elseif($group->contacts_count < 100) bg-yellow-500
                            @else bg-purple-500
                            @endif"></div>
                        
                        <!-- Group Info (Clickable Area) -->
                        <a href="{{ route('groups.show', $group) }}" class="flex-1 min-w-0 cursor-pointer">
                            <div class="flex items-start justify-between gap-4 mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-foreground group-hover:text-primary transition-colors truncate">
                                        {{ $group->name }}
                                    </h3>
                                    <p class="text-xs text-muted-foreground font-mono mt-0.5">ID: {{ $group->id }}</p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold flex-shrink-0 bg-primary/10 text-primary">
                                    {{ $group->contacts_count }} {{ $group->contacts_count === 1 ? 'membro' : 'membros' }}
                                </span>
                            </div>
                            
                            @if($group->description)
                                <p class="text-sm text-muted-foreground mb-3 line-clamp-1">
                                    {{ Str::limit($group->description, 100) }}
                                </p>
                            @else
                                <p class="text-sm text-muted-foreground mb-3 italic">
                                    Sem descrição
                                </p>
                            @endif
                            
                            <div class="flex items-center gap-4 text-xs text-muted-foreground flex-wrap">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Criado em {{ $group->created_at->format('d/m/Y') }}
                                </div>
                                @if($group->updated_at != $group->created_at)
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Atualizado {{ $group->updated_at->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                        </a>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <!-- Edit Button -->
                            <a href="{{ route('groups.edit', $group) }}" 
                               class="inline-flex items-center justify-center w-9 h-9 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                               title="Editar grupo"
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
                                        <a href="{{ route('groups.show', $group) }}" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-accent flex items-center gap-2 transition-colors">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver Detalhes
                                        </a>
                                        
                                        <div class="border-t border-border my-1"></div>
                                        
                                        <form action="{{ route('groups.destroy', $group) }}" 
                                              method="POST" 
                                              onsubmit="return handleDeleteConfirmation(event, 'Tem certeza que deseja eliminar este grupo?', 'Esta ação não pode ser desfeita.')" 
                                              class="block"
                                              onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar Grupo
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Arrow Icon -->
                            <a href="{{ route('groups.show', $group) }}" class="flex-shrink-0">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-foreground mb-2">Nenhum grupo encontrado</h3>
                    <p class="text-sm text-muted-foreground mb-6 max-w-sm mx-auto">
                        Crie o seu primeiro grupo para organizar contactos e facilitar campanhas
                    </p>
                    <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Criar Primeiro Grupo
                    </a>
                </div>
            @endif
        </div>
    </div>
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
            title: 'Eliminar Grupo',
            subtitle: subtitle,
            message: message,
            confirmText: 'Eliminar',
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
    const mainSearchInput = document.getElementById('searchGroupInput');
    
    if (mainSearchInput) {
        mainSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const groupItems = document.querySelectorAll('[data-group-id]');
            
            groupItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
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
