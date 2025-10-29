@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Analytics e Relatórios</h1>
            <p class="text-muted-foreground mt-1">Análise detalhada do sistema e métricas de performance</p>
        </div>
        <div class="flex gap-2">
            <button onclick="exportData('revenue')" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exportar
            </button>
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

    <!-- Revenue Analytics -->
    <div class="space-y-4 sm:space-y-6">
        <h2 class="text-xl font-semibold text-foreground">Análise de Receita</h2>
        
        <!-- Revenue Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Receita Total</p>
                        <p class="text-2xl font-bold text-foreground">R$ {{ number_format($revenueData['total_revenue'], 2, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Receita do Período</p>
                        <p class="text-2xl font-bold text-foreground">R$ {{ number_format($revenueData['period_revenue'], 2, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Receita Média por Usuário</p>
                        <p class="text-2xl font-bold text-foreground">R$ {{ number_format($revenueData['avg_revenue_per_user'], 2, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Planos Ativos</p>
                        <p class="text-2xl font-bold text-foreground">{{ $revenueData['revenue_by_plan']->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Receita por Mês</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Receita por Plano</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="revenueByPlanChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- User Analytics -->
    <div class="space-y-4 sm:space-y-6">
        <h2 class="text-xl font-semibold text-foreground">Análise de Usuárioes</h2>
        
        <!-- User Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Total de Usuárioes</p>
                        <p class="text-2xl font-bold text-foreground">{{ number_format($userData['user_status']->sum('count')) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Usuárioes Ativos</p>
                        <p class="text-2xl font-bold text-success">{{ number_format($userData['user_status']->where('is_active', true)->first()->count ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Taxa de Conversão</p>
                        <p class="text-2xl font-bold text-foreground">{{ number_format($conversionData['subscription_rate'], 1) }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Tempo Médio para Assinatura</p>
                        <p class="text-2xl font-bold text-foreground">{{ $conversionData['avg_time_to_subscription'] ? number_format($conversionData['avg_time_to_subscription'] / 24, 1) . ' dias' : 'N/A' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Crescimento de Usuárioes</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Distribuição de Usuárioes</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="userDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Analytics -->
    <div class="space-y-4 sm:space-y-6">
        <h2 class="text-xl font-semibold text-foreground">Análise de Campanhas</h2>
        
        <!-- Campaign Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Total de Campanhas</p>
                        <p class="text-2xl font-bold text-foreground">{{ number_format($campaignData['total_campaigns']) }}</p>
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
                        <p class="text-sm font-medium text-muted-foreground mb-2">Taxa de Sucesso</p>
                        <p class="text-2xl font-bold text-success">{{ number_format($campaignData['success_rate'], 1) }}%</p>
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
                        <p class="text-sm font-medium text-muted-foreground mb-2">Mensagens Enviadas</p>
                        <p class="text-2xl font-bold text-foreground">{{ number_format($campaignData['campaign_performance']->total_sent) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Destinatários Totais</p>
                        <p class="text-2xl font-bold text-foreground">{{ number_format($campaignData['campaign_performance']->total_recipients) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Campanhas por Status</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="campaignStatusChart"></canvas>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Campanhas por Tipo</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="campaignTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Analytics -->
    <div class="space-y-4 sm:space-y-6">
        <h2 class="text-xl font-semibold text-foreground">Análise WhatsApp</h2>
        
        <!-- WhatsApp Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Conexões Ativas</p>
                        <p class="text-2xl font-bold text-success">{{ number_format($whatsappData['active_connections']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Total de Grupos</p>
                        <p class="text-2xl font-bold text-foreground">{{ number_format($whatsappData['total_groups']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Total de Contatos</p>
                        <p class="text-2xl font-bold text-foreground">{{ number_format($whatsappData['total_contacts']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Taxa de Conexão</p>
                        <p class="text-2xl font-bold text-foreground">{{ number_format($conversionData['connection_rate'], 1) }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueData['revenue_by_month']->map(function($item) {
            return \Carbon\Carbon::create($item->year, $item->month, 1)->format('M Y');
        })) !!},
        datasets: [{
            label: 'Receita',
            data: {!! json_encode($revenueData['revenue_by_month']->pluck('revenue')) !!},
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
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
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});

// Revenue by Plan Chart
const revenueByPlanCtx = document.getElementById('revenueByPlanChart').getContext('2d');
const revenueByPlanChart = new Chart(revenueByPlanCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($revenueData['revenue_by_plan']->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($revenueData['revenue_by_plan']->pluck('total_revenue')) !!},
            backgroundColor: [
                'rgb(59, 130, 246)',
                'rgb(34, 197, 94)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)',
                'rgb(107, 114, 128)'
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

// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
const userGrowthChart = new Chart(userGrowthCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($userData['user_growth']->map(function($item) {
            return \Carbon\Carbon::parse($item->date)->format('d/m');
        })) !!},
        datasets: [{
            label: 'Novos Usuárioes',
            data: {!! json_encode($userData['user_growth']->pluck('count')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.8)'
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

// User Distribution Chart
const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
const userDistributionChart = new Chart(userDistributionCtx, {
    type: 'doughnut',
    data: {
        labels: ['Ativos', 'Inativos'],
        datasets: [{
            data: [
                {{ $userData['user_status']->where('is_active', true)->first()->count ?? 0 }},
                {{ $userData['user_status']->where('is_active', false)->first()->count ?? 0 }}
            ],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)'
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

// Campaign Status Chart
const campaignStatusCtx = document.getElementById('campaignStatusChart').getContext('2d');
const campaignStatusChart = new Chart(campaignStatusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($campaignData['campaigns_by_status']->pluck('status')->map(function($status) {
            return ucfirst($status);
        })) !!},
        datasets: [{
            data: {!! json_encode($campaignData['campaigns_by_status']->pluck('count')) !!},
            backgroundColor: [
                'rgb(34, 197, 94)',   // completed
                'rgb(245, 158, 11)',  // sending
                'rgb(59, 130, 246)',  // pending
                'rgb(239, 68, 68)',   // failed
                'rgb(107, 114, 128)'  // cancelled
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

// Campaign Type Chart
const campaignTypeCtx = document.getElementById('campaignTypeChart').getContext('2d');
const campaignTypeChart = new Chart(campaignTypeCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($campaignData['campaigns_by_type']->pluck('message_type')->map(function($type) {
            return ucfirst($type);
        })) !!},
        datasets: [{
            label: 'Campanhas',
            data: {!! json_encode($campaignData['campaigns_by_type']->pluck('count')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.8)'
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

// Export function
function exportData(type) {
    // Show info message using modal
    if (window.confirmationModal) {
        window.confirmationModal.show({
            title: 'Em Desenvolvimento',
            message: 'Funcionalidade de exportação será implementada em breve!',
            type: 'info',
            confirmText: 'OK',
            cancelText: ''
        });
    } else {
        alert('Funcionalidade de exportação será implementada em breve!');
    }
}
</script>
@endsection
