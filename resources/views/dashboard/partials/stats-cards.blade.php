<!-- Metric Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Card: Conexões -->
    <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div class="space-y-2">
                <p class="text-sm font-medium text-muted-foreground">Conexões Ativas</p>
                <p class="text-3xl font-bold text-foreground">{{ $stats['connections'] }}</p>
                <div class="flex items-center gap-1">
                    <span class="text-xs font-medium text-success">↑ WhatsApp conectados</span>
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

    <!-- Card: Contatos -->
    <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div class="space-y-2">
                <p class="text-sm font-medium text-muted-foreground">Total de Contatos</p>
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

