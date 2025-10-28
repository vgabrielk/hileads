@extends('layouts.app')

@section('title', 'Planos')

@section('content')
<div class="p-8 space-y-6">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-4">Escolha o seu Plano</h1>
        <p class="text-lg text-muted-foreground">Selecione o plano ideal para suas necessidades</p>
    </div>

    @if(session('success'))
        <div class="bg-success/10 border border-success/20 text-success px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    

    @if(session('info'))
        <div class="bg-primary/10 border border-primary/20 text-primary px-4 py-3 rounded-lg mb-6">
            {{ session('info') }}
        </div>
    @endif

    @if(auth()->user()->isAdmin())
        <div class="bg-primary/10 border border-primary/20 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="text-primary mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-primary">Acesso Administrativo</h3>
                        <p class="text-primary/80">Como administrador, tem acesso completo a todas as funcionalidades sem necessidade de subscrição.</p>
                    </div>
                </div>
                <a href="{{ route('plans.admin') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Gerir Planos
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="relative bg-card rounded-lg border-2 {{ $plan->is_popular ? 'border-primary' : 'border-border' }} overflow-hidden hover:shadow-md transition-shadow">
                @if($plan->is_popular)
                    <div class="absolute top-0 left-0 right-0 bg-primary text-primary-foreground text-center py-2 text-sm font-semibold">
                        Mais Popular
                    </div>
                @endif

                <div class="p-6 {{ $plan->is_popular ? 'pt-12' : '' }}">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-foreground mb-2">{{ $plan->name }}</h3>
                        <p class="text-muted-foreground">{{ $plan->description }}</p>
                    </div>

                    <div class="text-center mb-6">
                        <div class="text-3xl font-bold text-foreground mb-2">
                            {{ $plan->formatted_price }}
                        </div>
                        <div class="text-muted-foreground">{{ $plan->interval_description }}</div>
                    </div>

                    @if($plan->features)
                        <ul class="space-y-3 mb-6">
                            @foreach($plan->features as $feature)
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-foreground">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="space-y-2 mb-6 text-sm text-muted-foreground">
                        @if($plan->max_contacts)
                            <div>Máximo {{ number_format($plan->max_contacts) }} contactos</div>
                        @endif
                        @if($plan->max_campaigns)
                            <div>Máximo {{ $plan->max_campaigns }} campanhas</div>
                        @endif
                        @if($plan->max_mass_sendings)
                            <div>Máximo {{ $plan->max_mass_sendings }} campanhas</div>
                        @endif
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div class="w-full bg-muted text-muted-foreground font-semibold py-3 px-6 rounded-lg text-center">
                            Acesso Administrativo
                        </div>
                    @else
                        <button onclick="startCheckout({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price }})" class="w-full {{ $plan->is_popular ? 'bg-primary hover:bg-primary/90 text-primary-foreground' : 'bg-secondary hover:bg-secondary/80 text-secondary-foreground' }} font-semibold py-3 px-6 rounded-lg transition-colors inline-block text-center">
                            <i class="fas fa-credit-card me-2"></i>
                            Assinar Agora
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center mt-8">
        <p class="text-muted-foreground mb-4">Tem dúvidas sobre os planos?</p>
        <a href="#" class="text-primary hover:text-primary/80 font-semibold">Inicie sessão em contacto conosco</a>
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
