@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Detalhes da Subscrição</h1>
            <p class="text-muted-foreground mt-1">Visualize e faça a gestão esta subscrição</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.subscriptions.edit', $subscription) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-warning-foreground bg-warning hover:bg-warning/90 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Subscription Details -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID da Subscrição</label>
                        <p class="text-foreground font-mono text-sm">{{ $subscription->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($subscription->status === 'active') bg-success/10 text-success
                            @elseif($subscription->status === 'pending') bg-warning/10 text-warning
                            @elseif($subscription->status === 'cancelled') bg-destructive/10 text-destructive
                            @elseif($subscription->status === 'expired') bg-muted/10 text-muted-foreground
                            @else bg-destructive/10 text-destructive @endif">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Valor</label>
                        <p class="text-foreground font-semibold">€{{ number_format($subscription->amount ?? $subscription->plan->price, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Plano</label>
                        <p class="text-foreground">{{ $subscription->plan->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Data de Início</label>
                        <p class="text-foreground">{{ $subscription->starts_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Data de Expiração</label>
                        <p class="text-foreground">{{ $subscription->expires_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Criado em</label>
                        <p class="text-foreground">{{ $subscription->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($subscription->cancelled_at)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Cancelado em</label>
                        <p class="text-foreground">{{ $subscription->cancelled_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- User Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações do Utilizador</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Nome</label>
                        <p class="text-foreground">{{ $subscription->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">E-mail</label>
                        <p class="text-foreground">{{ $subscription->user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID do Utilizador</label>
                        <p class="text-foreground font-mono text-sm">{{ $subscription->user->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Tipo de Utilizador</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $subscription->user->isAdmin() ? 'bg-primary/10 text-primary' : 'bg-muted/10 text-muted-foreground' }}">
                            {{ $subscription->user->isAdmin() ? 'Administrador' : 'Utilizador' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stripe Info -->
            @if($subscription->stripe_subscription_id || $subscription->stripe_customer_id)
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações do Stripe</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($subscription->stripe_subscription_id)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID da Subscrição Stripe</label>
                        <p class="text-foreground font-mono text-sm">{{ $subscription->stripe_subscription_id }}</p>
                    </div>
                    @endif
                    @if($subscription->stripe_customer_id)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID do Cliente Stripe</label>
                        <p class="text-foreground font-mono text-sm">{{ $subscription->stripe_customer_id }}</p>
                    </div>
                    @endif
                    @if($subscription->stripe_session_id)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">ID da Sessão Stripe</label>
                        <p class="text-foreground font-mono text-sm">{{ $subscription->stripe_session_id }}</p>
                    </div>
                    @endif
                    @if($subscription->stripe_status)
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Status Stripe</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                            {{ ucfirst($subscription->stripe_status) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($subscription->notes)
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Observações</h3>
                <p class="text-foreground whitespace-pre-wrap">{{ $subscription->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Quick Actions -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ações Rápidas</h3>
                <div class="space-y-2">
                    @if($subscription->status === 'active')
                        <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Tem certeza que deseja cancelar esta subscrição?')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar Subscrição
                            </button>
                        </form>
                    @elseif($subscription->status === 'cancelled')
                        <form method="POST" action="{{ route('admin.subscriptions.reactivate', $subscription) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Tem certeza que deseja reativar esta subscrição?')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-success-foreground bg-success hover:bg-success/90 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reativar Subscrição
                            </button>
                        </form>
                    @endif

                    @if($subscription->stripe_subscription_id)
                        <form method="POST" action="{{ route('admin.subscriptions.sync', $subscription) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Sincronizar com Stripe
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription) }}" class="inline-block w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Tem certeza que deseja eliminar esta subscrição? Esta ação não pode ser desfeita.')"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Eliminar Subscrição
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stripe Data -->
            @if($stripeData)
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Dados do Stripe</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="font-medium text-muted-foreground">Status:</span>
                        <span class="text-foreground">{{ $stripeData->status }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-muted-foreground">Período Atual:</span>
                        <span class="text-foreground">
                            @if($stripeData->current_period_start && $stripeData->current_period_end)
                                {{ \Carbon\Carbon::createFromTimestamp($stripeData->current_period_start)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::createFromTimestamp($stripeData->current_period_end)->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    @if($stripeData->cancel_at_period_end)
                    <div>
                        <span class="font-medium text-muted-foreground">Cancelar no fim do período:</span>
                        <span class="text-destructive">Sim</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
