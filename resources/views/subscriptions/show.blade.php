@extends('layouts.app')

@section('title', 'Detalhes da Subscrição')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar para subscrições
        </a>
        <div class="flex items-center gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">{{ $subscription->plan->name }}</h1>
            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold
                @if($subscription->status === 'active') bg-green-100 text-green-700 border border-green-200
                @elseif($subscription->status === 'pending') bg-amber-100 text-amber-700 border border-amber-200
                @elseif($subscription->status === 'cancelled') bg-red-100 text-red-700 border border-red-200
                @else bg-gray-100 text-gray-700 border border-gray-200
                @endif">
                <span class="w-2 h-2 rounded-full mr-2
                    @if($subscription->status === 'active') bg-green-500
                    @elseif($subscription->status === 'pending') bg-amber-500
                    @elseif($subscription->status === 'cancelled') bg-red-500
                    @else bg-gray-500 @endif">
                </span>
                @switch($subscription->status)
                    @case('active') Ativa @break
                    @case('pending') Pendente @break
                    @case('cancelled') Cancelada @break
                    @case('expired') Expirada @break
                    @default {{ ucfirst($subscription->status) }}
                @endswitch
            </span>
        </div>
        <p class="text-muted-foreground mt-1">{{ $subscription->plan->description }}</p>
    </div>

    @if(session('success'))
        <div class="bg-success/10 border border-success/20 text-success px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-destructive/10 border border-destructive/20 text-destructive px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Subscription Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Plan Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-base font-semibold text-foreground">Detalhes do Plano</h2>
                        <p class="text-sm text-muted-foreground">Recursos e limites</p>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-border">
                <div class="flex items-center justify-between px-6 py-4">
                    <span class="text-sm text-muted-foreground">Preço</span>
                    <span class="text-sm font-semibold text-foreground">{{ $subscription->plan->formatted_price }}</span>
                </div>
                <div class="flex items-center justify-between px-6 py-4">
                    <span class="text-sm text-muted-foreground">Período</span>
                    <span class="text-sm font-semibold text-foreground">{{ $subscription->plan->interval_description }}</span>
                </div>
                @if($subscription->plan->max_contacts)
                    <div class="flex items-center justify-between px-6 py-4">
                        <span class="text-sm text-muted-foreground">Contactos</span>
                        <span class="text-sm font-semibold text-foreground">{{ number_format($subscription->plan->max_contacts) }}</span>
                    </div>
                @endif
                @if($subscription->plan->max_campaigns)
                    <div class="flex items-center justify-between px-6 py-4">
                        <span class="text-sm text-muted-foreground">Campanhas</span>
                        <span class="text-sm font-semibold text-foreground">{{ $subscription->plan->max_campaigns }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Subscription Timeline -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-base font-semibold text-foreground">Cronologia da Subscrição</h2>
                        <p class="text-sm text-muted-foreground">Datas importantes</p>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-border">
                <div class="flex items-center gap-4 px-6 py-4">
                    <div class="w-1 h-12 rounded-full flex-shrink-0 bg-blue-500"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Data de início</p>
                        <p class="text-sm font-medium text-foreground">{{ $subscription->starts_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                    <svg class="w-4 h-4 text-muted-foreground flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>

                <div class="flex items-center gap-4 px-6 py-4">
                    <div class="w-1 h-12 rounded-full flex-shrink-0 bg-orange-500"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Data de expiração</p>
                        <p class="text-sm font-medium text-foreground">{{ $subscription->expires_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                    <svg class="w-4 h-4 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                @if($subscription->cancelled_at)
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="w-1 h-12 rounded-full flex-shrink-0 bg-red-500"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Data de cancelamento</p>
                            <p class="text-sm font-medium text-foreground">{{ $subscription->cancelled_at->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                @endif

                @if($subscription->status === 'active')
                    <div class="flex items-center gap-4 px-6 py-4 bg-green-50">
                        <div class="w-1 h-12 rounded-full flex-shrink-0 bg-green-500"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Dias restantes</p>
                            <p class="text-sm font-medium text-green-700">{{ $subscription->days_remaining }} de {{ $subscription->total_days }} dias ({{ number_format($subscription->progress_percentage, 1) }}%)</p>
                        </div>
                        <div class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            Ativa
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Features -->
    @if($subscription->plan->features)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-base font-semibold text-foreground">Recursos Inclusos</h2>
                        <p class="text-sm text-muted-foreground">Tudo que está disponível no seu plano</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($subscription->plan->features as $feature)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-foreground">{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 bg-muted/30 rounded-xl p-6">
        <div class="text-sm text-muted-foreground">
            Criada em {{ $subscription->created_at->format('d/m/Y H:i') }}
        </div>
        <div class="flex gap-2">
            @if($subscription->status === 'active')
                <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-destructive text-destructive-foreground rounded-lg text-sm font-medium hover:bg-destructive/90 transition-all"
                            onclick="return handleCancelSubscription(event, 'Tem certeza que deseja cancelar esta subscrição?', 'Esta ação não pode ser desfeita.')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar Subscrição
                    </button>
                </form>
            @elseif($subscription->status === 'cancelled' || $subscription->status === 'expired')
                <a href="{{ route('plans.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Assinar Novamente
                </a>
            @endif
        </div>
    </div>
</div>
@endsection

<script>
// Handle subscription cancellation confirmation with modal
async function handleCancelSubscription(event, message, subtitle) {
    event.preventDefault();
    
    const confirmed = await confirmAction({
        type: 'danger',
        title: 'Cancelar Subscrição',
        subtitle: subtitle,
        message: message,
        confirmText: 'Cancelar Subscrição',
        cancelText: 'Manter Subscrição'
    });
    
    if (confirmed) {
        // Submit the form
        event.target.submit();
    }
    
    return false; // Prevent default form submission
}
</script>
