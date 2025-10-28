@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Estatísticas de Campanhas</h1>
            <p class="text-muted-foreground mt-1">Análise detalhada das campanhas do sistema</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.campaigns.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Período</label>
                <select name="period" class="px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="7" {{ $period == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
                    <option value="30" {{ $period == '30' ? 'selected' : '' }}>Últimos 30 dias</option>
                    <option value="90" {{ $period == '90' ? 'selected' : '' }}>Últimos 90 dias</option>
                    <option value="365" {{ $period == '365' ? 'selected' : '' }}>Último ano</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Atualizar
            </button>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Total de Campanhas</p>
                    <p class="text-2xl font-bold text-foreground">{{ number_format($stats['total_campaigns']) }}</p>
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
                    <p class="text-2xl font-bold text-success">{{ number_format($stats['completed_campaigns']) }}</p>
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
                    <p class="text-sm font-medium text-muted-foreground mb-2">Taxa de Sucesso</p>
                    <p class="text-2xl font-bold text-foreground">{{ $stats['success_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
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

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Campaigns by Day Chart -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Campanhas por Dia</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="campaignsChart"></canvas>
            </div>
        </div>

        <!-- Campaigns by Status Chart -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Campanhas por Status</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Users -->
    <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
        <h3 class="text-lg font-semibold text-foreground mb-4">Top Utilizadores por Campanhas</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Utilizador</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Campanhas</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Total de Destinatários</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Última Campanha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($topUsers as $user)
                        <tr class="hover:bg-muted/30">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-foreground">{{ $user->user->name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ $user->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-foreground">{{ number_format($user->campaign_count) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-foreground">{{ number_format($user->total_recipients) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-muted-foreground">{{ $user->user->massSendings()->latest()->first()?->created_at->format('d/m/Y') ?? 'N/A' }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">
                                Nenhum utilizador encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Campaigns by Day Chart
const campaignsCtx = document.getElementById('campaignsChart').getContext('2d');
const campaignsChart = new Chart(campaignsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($campaignsByDay->map(function($item) {
            return \Carbon\Carbon::parse($item->date)->format('d/m');
        })) !!},
        datasets: [{
            label: 'Campanhas',
            data: {!! json_encode($campaignsByDay->pluck('count')) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($campaignsByStatus->pluck('status')->map(function($status) {
            return ucfirst($status);
        })) !!},
        datasets: [{
            data: {!! json_encode($campaignsByStatus->pluck('count')) !!},
            backgroundColor: [
                'rgb(34, 197, 94)',   // completed - green
                'rgb(245, 158, 11)',  // sending - yellow
                'rgb(59, 130, 246)',  // pending - blue
                'rgb(239, 68, 68)',   // failed - red
                'rgb(107, 114, 128)'  // cancelled - gray
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection
