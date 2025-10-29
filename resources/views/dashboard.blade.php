@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-foreground">Dashboard</h1>
        <p class="text-muted-foreground mt-1">Visão geral do sistema</p>
    </div>

    <!-- Access Status Card (Carregamento assíncrono) -->
    <div id="access-status-container" data-async-load="{{ route('api.dashboard.access-status') }}" data-async-cache="true" data-async-cache-duration="120000">
        <!-- Skeleton Loader -->
        <div class="relative rounded-lg border border-border bg-card p-4 flex items-start gap-3 animate-pulse">
            <div class="w-5 h-5 bg-gray-200 rounded flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 rounded w-40"></div>
                <div class="h-3 bg-gray-200 rounded w-64"></div>
            </div>
        </div>
    </div>

    <!-- Metric Cards (Carregamento assíncrono) -->
    <div id="stats-cards-container" data-async-load="{{ route('api.dashboard.stats') }}" data-async-cache="true" data-async-cache-duration="300000">
        <!-- Skeleton Loader -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @for($i = 0; $i < 4; $i++)
                <x-skeleton-card />
            @endfor
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Connections -->
        <div class="bg-card rounded-lg border border-border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground">Conexões Recentes</h3>
                <a href="{{ route('whatsapp.index') }}" class="text-primary hover:text-primary/80 text-sm font-medium flex items-center gap-1">
                    Ver todas
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            
            <!-- Carregamento assíncrono -->
            <div id="recent-connections-container" data-async-load="{{ route('api.dashboard.recent-connections') }}" data-async-cache="true" data-async-cache-duration="120000">
                <!-- Skeleton Loader -->
                <div class="space-y-3">
                    @for($i = 0; $i < 3; $i++)
                        <x-skeleton-list-item />
                    @endfor
                </div>
            </div>
        </div>

        <!-- Recent Groups -->
        <div class="bg-card rounded-lg border border-border p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Grupos Recentes</h3>
            
            <!-- Carregamento assíncrono -->
            <div id="recent-groups-container" data-async-load="{{ route('api.dashboard.recent-groups') }}" data-async-cache="true" data-async-cache-duration="120000">
                <!-- Skeleton Loader -->
                <div class="space-y-3">
                    @for($i = 0; $i < 3; $i++)
                        <x-skeleton-list-item />
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Contatos Recentes</h2>
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
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Contato</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Grupo</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Estado</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-foreground">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border" id="recent-contacts-container" data-async-load="{{ route('api.dashboard.recent-contacts') }}" data-async-cache="true" data-async-cache-duration="120000">
                    <!-- Skeleton Loader -->
                    @for($i = 0; $i < 5; $i++)
                        <x-skeleton-table-row />
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
