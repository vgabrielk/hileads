@extends('layouts.app')

@section('title', 'Minhas Assinaturas')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Minhas Assinaturas</h1>
        <p class="text-muted-foreground mt-1">Faça a gestão suas assinaturas e histórico de pagamentos</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if(session('admin_message'))
        <div class="text-center py-12">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-8 max-w-2xl mx-auto">
                <div class="text-blue-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-4">Acesso Administrativo</h3>
                <p class="text-blue-700 mb-6">Como administrador, tem acesso completo a todas as funcionalidades do sistema sem necessidade de assinatura.</p>
                <div class="bg-white rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Recursos Disponíveis:</h4>
                    <ul class="text-left text-gray-700 space-y-1">
                        <li>✓ Contatos ilimitados</li>
                        <li>✓ Campanhas ilimitadas</li>
                        <li>✓ Campanhas ilimitadas</li>
                        <li>✓ Acesso a todas as funcionalidades</li>
                        <li>✓ Gestão de usuárioes</li>
                    </ul>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
    @elseif($subscriptions->count() > 0)
        <!-- Subscriptions List - Consistent View for All Resolutions -->
        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <!-- Card Header -->
            <div class="px-6 py-5 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-base font-semibold text-foreground">Assinaturas Ativas</h2>
                        <p class="text-sm text-muted-foreground">Gerencie suas assinaturas e histórico de pagamentos</p>
                    </div>
                </div>
            </div>

            <!-- Subscriptions List Items -->
            <div class="divide-y divide-border">
                @foreach($subscriptions as $subscription)
                    <div class="p-6">
                        <!-- Subscription Header -->
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-base font-semibold text-foreground">{{ $subscription->plan->name }}</h3>
                                </div>
                                <p class="text-sm text-muted-foreground">{{ $subscription->plan->description }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold flex-shrink-0
                                @if($subscription->status === 'active') bg-green-100 text-green-800
                                @elseif($subscription->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($subscription->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @switch($subscription->status)
                                    @case('active') Ativa @break
                                    @case('pending') Pendente @break
                                    @case('cancelled') Cancelada @break
                                    @case('expired') Expirada @break
                                    @default {{ ucfirst($subscription->status) }}
                                @endswitch
                            </span>
                        </div>

                        <!-- Nested Payment Card -->
                        <div class="bg-muted/30 rounded-lg p-4 mb-4">
                            <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider mb-3">Informações do Pagamento</p>
                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                <div class="flex-1 min-w-[200px]">
                                    <div class="text-sm font-semibold text-foreground mb-1">{{ $subscription->plan->name }}</div>
                                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $subscription->starts_at->format('d/m/Y') }} - {{ $subscription->expires_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-primary">
                                    {{ $subscription->plan->formatted_price }}<span class="text-sm font-normal text-muted-foreground">/{{ $subscription->plan->interval_description }}</span>
                                </div>
                            </div>

                            @if($subscription->status === 'active')
                                <div class="mt-4">
                                    <div class="flex items-center justify-between text-xs text-muted-foreground mb-2">
                                        <span>Dias restantes</span>
                                        <span class="font-semibold text-foreground">{{ $subscription->days_remaining }} de {{ $subscription->total_days }} dias</span>
                                    </div>
                                    <div class="w-full bg-border rounded-full h-2">
                                        <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: {{ number_format($subscription->progress_percentage, 2) }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('subscriptions.show', $subscription) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver Detalhes
                            </a>
                            
                            @if($subscription->status === 'active')
                                <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" class="inline m-0">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center px-4 py-2 bg-destructive/10 text-destructive rounded-lg text-sm font-medium hover:bg-destructive/20 transition-all"
                                            onclick="return handleCancelSubscription(event, 'Tem certeza que deseja cancelar esta assinatura?', 'Esta ação não pode ser desfeita.')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhuma assinatura encontrada</h3>
            <p class="text-gray-600 mb-6">O usuário ainda não possui nenhuma assinatura ativa.</p>
            <a href="{{ route('plans.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Ver Planos Disponíveis
            </a>
        </div>
    @endif
</div>

<script>
// Handle subscription cancellation confirmation with modal
async function handleCancelSubscription(event, message, subtitle) {
    event.preventDefault();
    
    const confirmed = await confirmAction({
        type: 'danger',
        title: 'Cancelar Assinatura',
        subtitle: subtitle,
        message: message,
        confirmText: 'Cancelar Assinatura',
        cancelText: 'Manter Assinatura'
    });
    
    if (confirmed) {
        // Submit the form - get the form from the button
        const form = event.target.closest('form');
        if (form) {
            form.submit();
        }
    }
    
    return false; // Prevent default form submission
}
</script>
@endsection
