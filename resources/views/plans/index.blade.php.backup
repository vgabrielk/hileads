@extends('layouts.app')

@section('title', 'Planos')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Planos e Assinaturas</h1>
        <p class="text-muted-foreground mt-1">Selecione o plano ideal para suas necessidades</p>
    </div>

    @if(session('success'))
        <div class="bg-success/10 border border-success/20 text-success px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-primary/10 border border-primary/20 text-primary px-4 py-3 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    @if(auth()->user()->isAdmin())
        <div class="bg-primary/10 border border-primary/20 rounded-xl p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-primary">Acesso Administrativo</h3>
                        <p class="text-sm text-primary/80 mt-1">Como administrador, tem acesso completo a todas as funcionalidades sem necessidade de assinatura.</p>
                    </div>
                </div>
                <a href="{{ route('plans.admin') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Gerenciar Planos
                </a>
            </div>
        </div>
    @endif

    <!-- Plans List - Consistent View for All Resolutions -->
    <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
        <!-- Card Header -->
        <div class="px-6 py-5 border-b border-border">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-base font-semibold text-foreground">Planos Disponíveis</h2>
                    <p class="text-sm text-muted-foreground">Escolha o plano que melhor se adequa às suas necessidades e comece a usar todas as funcionalidades</p>
                </div>
            </div>
        </div>

        <!-- Plans List Items -->
        <div class="divide-y divide-border">
            @php
                $colors = ['green', 'orange', 'yellow', 'purple', 'blue', 'pink'];
            @endphp
            @foreach($plans as $index => $plan)
                @php
                    $color = $colors[$index % count($colors)];
                @endphp
                <div class="flex items-center gap-4 px-6 py-5 hover:bg-accent/30 transition-all duration-200 group relative cursor-pointer" 
                     @if(!auth()->user()->isAdmin()) onclick="startCheckout({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price }})" @endif>
                    <!-- Status Indicator Bar -->
                    <div class="w-1 h-20 rounded-full flex-shrink-0 bg-{{ $color }}-500"></div>
                    
                    <!-- Plan Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-base font-semibold text-foreground group-hover:text-primary transition-colors">
                                        {{ $plan->name }}
                                    </h3>
                                    @if($plan->is_popular)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-primary text-primary-foreground">
                                            Popular
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-muted-foreground mb-2">{{ $plan->description }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 text-sm flex-wrap">
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-foreground">{{ $plan->formatted_price }}</span>
                                <span class="text-muted-foreground">{{ $plan->interval_description }}</span>
                            </div>
                            @if($plan->max_contacts)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-muted text-muted-foreground">
                                    {{ number_format($plan->max_contacts) }} contatos
                                </span>
                            @endif
                            @if($plan->max_campaigns)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-muted text-muted-foreground">
                                    {{ $plan->max_campaigns }} campanhas
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Arrow Icon / Admin Badge -->
                    <div class="flex-shrink-0">
                        @if(auth()->user()->isAdmin())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                Admin
                            </span>
                        @else
                            <svg class="w-5 h-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Help Section -->
    <div class="bg-muted rounded-xl p-6 text-center">
        <p class="text-foreground mb-2">Tem dúvidas sobre os planos?</p>
        <p class="text-sm text-muted-foreground">Entre em contato connosco para mais informações</p>
    </div>
</div>

<!-- Loading Overlay -->
<div id="checkout-loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div style="text-align: center; background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);">
        <!-- Simple Spinner -->
        <div style="width: 50px; height: 50px; margin: 0 auto 20px; border: 4px solid #e5e7eb; border-top: 4px solid #25D366; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        
        <!-- Loading Text -->
        <p style="color: #262626; font-size: 16px; font-weight: 500; margin: 0;">Processando pagamento...</p>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<script>
// Show loading overlay
function showLoadingOverlay() {
    const overlay = document.getElementById('checkout-loading-overlay');
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

// Hide loading overlay
function hideLoadingOverlay() {
    document.getElementById('checkout-loading-overlay').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Start checkout process
function startCheckout(planId, planName, planPrice) {
    // Show loading overlay
    showLoadingOverlay();
    
    // Redirect directly to checkout page endpoint
    window.location.href = `/plans/${planId}/checkout-page`;
}

// Handle page visibility change
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        console.log('Página oculta - redirecionamento pode ter falhado');
    } else {
        console.log('Página visível novamente');
    }
});
</script>
@endsection
