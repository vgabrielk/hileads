@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Gerir Planos</h1>
            <p class="text-muted-foreground mt-1">Crie, edite e faça a gestão os planos de subscrição</p>
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
                <tbody class="divide-y divide-border">
                    @forelse($plans as $plan)
                    <tr class="hover:bg-muted/25 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($plan->is_popular)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary text-primary-foreground">
                                            Popular
                                        </span>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-foreground">{{ $plan->name }}</div>
                                    @if($plan->description)
                                        <div class="text-sm text-muted-foreground">{{ $plan->description }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-foreground">{{ $plan->formatted_price }}</div>
                            <div class="text-sm text-muted-foreground">{{ $plan->interval_description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($plan->interval === 'monthly') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ $plan->interval === 'monthly' ? 'Mensal' : 'Anual' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                            <div class="space-y-1">
                                @if($plan->max_contacts)
                                    <div>Contactos: {{ number_format($plan->max_contacts) }}</div>
                                @else
                                    <div>Contactos: Ilimitado</div>
                                @endif
                                
                                @if($plan->max_campaigns)
                                    <div>Campanhas: {{ $plan->max_campaigns }}</div>
                                @else
                                    <div>Campanhas: Ilimitado</div>
                                @endif
                                
                                @if($plan->max_mass_sendings)
                                    <div>Envios: {{ $plan->max_mass_sendings }}</div>
                                @else
                                    <div>Envios: Ilimitado</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($plan->is_active) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                            {{ $plan->sort_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('plans.edit', $plan) }}" class="text-primary hover:text-primary/80 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <form method="POST" action="{{ route('plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja eliminar este plano? Esta ação não pode ser desfeita.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-destructive hover:text-destructive/80 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-muted-foreground">
                                <svg class="mx-auto h-12 w-12 text-muted-foreground mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-foreground mb-2">Nenhum plano encontrado</h3>
                                <p class="text-sm text-muted-foreground mb-4">Comece criando seu primeiro plano de subscrição.</p>
                                <a href="{{ route('plans.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Criar Primeiro Plano
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Features Summary -->
    @if($plans->count() > 0)
    <div class="bg-card rounded-lg border border-border p-6">
        <h3 class="text-lg font-semibold text-foreground mb-4">Resumo dos Planos</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-primary">{{ $plans->count() }}</div>
                <div class="text-sm text-muted-foreground">Total de Planos</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-success">{{ $plans->where('is_active', true)->count() }}</div>
                <div class="text-sm text-muted-foreground">Planos Ativos</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-warning">{{ $plans->where('is_popular', true)->count() }}</div>
                <div class="text-sm text-muted-foreground">Planos Populares</div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
