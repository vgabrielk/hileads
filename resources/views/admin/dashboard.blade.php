@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Dashboard Administrativo</h1>
        <p class="text-muted-foreground mt-1">Visão geral do sistema e estatísticas</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Users -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Total de Utilizadores</p>
                    <p class="text-2xl font-bold text-foreground">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-success mt-1">+{{ $newUsersLast30Days }} nos últimos 30 dias</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Subscrições Ativas</p>
                    <p class="text-2xl font-bold text-foreground">{{ number_format($stats['active_subscriptions']) }}</p>
                    <p class="text-xs text-muted-foreground mt-1">de {{ number_format($stats['total_subscriptions']) }} total</p>
                </div>
                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Receita Mensal</p>
                    <p class="text-2xl font-bold text-foreground">R$ {{ number_format($monthlyRevenue, 2, ',', '.') }}</p>
                    <p class="text-xs text-muted-foreground mt-1">Este mês</p>
                </div>
                <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- WhatsApp Connections -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground mb-2">Ligações WhatsApp</p>
                    <p class="text-2xl font-bold text-foreground">{{ number_format($stats['active_whatsapp_connections']) }}</p>
                    <p class="text-xs text-muted-foreground mt-1">de {{ number_format($stats['total_whatsapp_connections']) }} total</p>
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
        <!-- Users Growth Chart -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Crescimento de Utilizadores</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="usersChart"></canvas>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Receita Mensal</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Plans Distribution -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Distribuição por Plano</h3>
            <div class="space-y-3">
                @foreach($usersByPlan as $plan)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-primary"></div>
                        <span class="text-sm font-medium text-foreground">{{ $plan->name }}</span>
                    </div>
                    <span class="text-sm text-muted-foreground">{{ $plan->subscriptions_count }} utilizadores</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Subscriptions Status -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Status das Subscrições</h3>
            <div class="space-y-3">
                @foreach($subscriptionsByStatus as $status)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full 
                            @if($status->status === 'active') bg-success
                            @elseif($status->status === 'pending') bg-warning
                            @elseif($status->status === 'cancelled') bg-destructive
                            @else bg-muted @endif"></div>
                        <span class="text-sm font-medium text-foreground">{{ ucfirst($status->status) }}</span>
                    </div>
                    <span class="text-sm text-muted-foreground">{{ $status->count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- System Stats -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Estatísticas do Sistema</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">Total de Grupos</span>
                    <span class="text-sm font-medium text-foreground">{{ number_format($stats['total_groups']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">Total de Contactos</span>
                    <span class="text-sm font-medium text-foreground">{{ number_format($stats['total_contacts']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">Total de Campanhas</span>
                    <span class="text-sm font-medium text-foreground">{{ number_format($stats['total_campaigns']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">Planos Ativos</span>
                    <span class="text-sm font-medium text-foreground">{{ number_format($stats['active_plans']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Recent Users -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Utilizadores Recentes</h3>
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-primary">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-foreground truncate">{{ $user->name }}</p>
                        <p class="text-xs text-muted-foreground">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Subscriptions -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Subscrições Recentes</h3>
            <div class="space-y-3">
                @foreach($recentSubscriptions as $subscription)
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-foreground truncate">{{ $subscription->user->name }}</p>
                        <p class="text-xs text-muted-foreground">{{ $subscription->plan->name }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full 
                        @if($subscription->status === 'active') bg-success/10 text-success
                        @elseif($subscription->status === 'pending') bg-warning/10 text-warning
                        @else bg-destructive/10 text-destructive @endif">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Connections -->
        <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Ligações Recentes</h3>
            <div class="space-y-3">
                @foreach($recentConnections as $connection)
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-foreground truncate">{{ $connection->user->name }}</p>
                        <p class="text-xs text-muted-foreground">{{ $connection->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Users Growth Chart
const usersCtx = document.getElementById('usersChart').getContext('2d');
const usersChart = new Chart(usersCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($usersByMonth->map(function($item) {
            return Carbon\Carbon::create($item->year, $item->month)->format('M/Y');
        })) !!},
        datasets: [{
            label: 'Novos Utilizadores',
            data: {!! json_encode($usersByMonth->pluck('count')) !!},
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

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($revenueByMonth->map(function($item) {
            return Carbon\Carbon::create($item->year, $item->month)->format('M/Y');
        })) !!},
        datasets: [{
            label: 'Receita (R$)',
            data: {!! json_encode($revenueByMonth->pluck('revenue')) !!},
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgb(34, 197, 94)',
            borderWidth: 1
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
</script>
@endsection
