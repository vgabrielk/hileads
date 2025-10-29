@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Gerenciar Planos</h1>
            <p class="text-muted-foreground mt-1">Crie, edite e faça a gestão os planos de assinatura</p>
        </div>
        <a href="{{ route('plans.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors w-full sm:w-auto justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Novo Plano
        </a>
    </div>

    <!-- Plans Table -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Plano</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Preço</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Intervalo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Limites</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Ordem</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody id="admin-plans-tbody" class="divide-y divide-border" 
                       data-async-load="{{ route('api.admin.plans.list') }}" 
                       data-async-cache="true" 
                       data-async-cache-duration="300000">
                    <!-- Skeleton Loaders -->
                    @for($i = 0; $i < 5; $i++)
                        <tr class="animate-pulse">
                            <td class="px-6 py-4">
                                <div class="h-4 bg-gray-200 rounded w-32"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 bg-gray-200 rounded w-24"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-6 bg-gray-200 rounded-full w-16"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <div class="h-3 bg-gray-200 rounded w-24"></div>
                                    <div class="h-3 bg-gray-200 rounded w-20"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-6 bg-gray-200 rounded-full w-16"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="h-4 bg-gray-200 rounded w-8"></div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="h-4 bg-gray-200 rounded w-16 ml-auto"></div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Features Summary (carrega junto com a tabela) -->
    <div id="plans-summary" class="bg-card rounded-lg border border-border p-6">
        <h3 class="text-lg font-semibold text-foreground mb-4">Resumo dos Planos</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center animate-pulse">
                <div class="h-8 bg-gray-200 rounded w-12 mx-auto mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-24 mx-auto"></div>
            </div>
            <div class="text-center animate-pulse">
                <div class="h-8 bg-gray-200 rounded w-12 mx-auto mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-24 mx-auto"></div>
            </div>
            <div class="text-center animate-pulse">
                <div class="h-8 bg-gray-200 rounded w-12 mx-auto mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-24 mx-auto"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Atualizar resumo quando a tabela carregar
document.getElementById('admin-plans-tbody').addEventListener('async-loaded', function(e) {
    const data = e.detail.data;
    const plans = Array.isArray(data) ? data : (data.plans || []);
    
    const totalPlans = plans.length;
    const activePlans = plans.filter(p => p.is_active).length;
    const popularPlans = plans.filter(p => p.is_popular).length;
    
    document.getElementById('plans-summary').innerHTML = `
        <h3 class="text-lg font-semibold text-foreground mb-4">Resumo dos Planos</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-primary">${totalPlans}</div>
                <div class="text-sm text-muted-foreground">Total de Planos</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-success">${activePlans}</div>
                <div class="text-sm text-muted-foreground">Planos Ativos</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-warning">${popularPlans}</div>
                <div class="text-sm text-muted-foreground">Planos Populares</div>
            </div>
        </div>
    `;
});
</script>
@endsection

