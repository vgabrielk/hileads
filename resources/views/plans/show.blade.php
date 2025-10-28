@extends('layouts.app')

@section('title', $plan->name)

@section('content')
<div class="p-8 space-y-6">
        <div class="bg-white rounded-2xl shadow-lg border-2 {{ $plan->is_popular ? 'border-blue-500' : 'border-gray-200' }} overflow-hidden">
            @if($plan->is_popular)
                <div class="bg-blue-500 text-white text-center py-3 text-lg font-semibold">
                    Plano Mais Popular
                </div>
            @endif

            <div class="p-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $plan->name }}</h1>
                    <p class="text-xl text-gray-600 mb-6">{{ $plan->description }}</p>
                    
                    <div class="text-5xl font-bold text-gray-900 mb-2">
                        {{ $plan->formatted_price }}
                    </div>
                    <div class="text-gray-600 text-lg">{{ $plan->interval_description }}</div>
                </div>

                @if($plan->features)
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Recursos Inclusos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($plan->features as $feature)
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700 text-lg">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Limites do Plano</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($plan->max_contacts)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($plan->max_contacts) }}</div>
                                <div class="text-gray-600">Contactos</div>
                            </div>
                        @endif
                        @if($plan->max_campaigns)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $plan->max_campaigns }}</div>
                                <div class="text-gray-600">Campanhas</div>
                            </div>
                        @endif
                        @if($plan->max_mass_sendings)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $plan->max_mass_sendings }}</div>
                                <div class="text-gray-600">Campanhas</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="text-center">
                    @if(auth()->user()->isAdmin())
                        <div class="bg-gray-300 text-gray-600 font-bold py-4 px-8 rounded-lg text-xl inline-block">
                            Acesso Administrativo
                        </div>
                        <div class="mt-4 text-gray-600">
                            Como administrador, tem acesso completo a todas as funcionalidades.
                        </div>
                    @else
                        <button onclick="startCheckout({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price }})" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg text-xl transition duration-200 inline-block">
                            <i class="fas fa-credit-card me-2"></i>
                            Assinar {{ $plan->name }}
                        </button>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('plans.index') }}" class="text-gray-600 hover:text-gray-800 font-semibold">
                            ← Voltar para todos os planos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="checkout-loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 100%); z-index: 9999; display: none; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
    <div style="background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%); border-radius: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1); padding: 40px; text-align: center; max-width: 480px; width: 90%; position: relative; overflow: hidden;">
        
        <!-- Animated Background -->
        <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: linear-gradient(45deg, transparent 30%, rgba(59, 130, 246, 0.1) 50%, transparent 70%); animation: shimmer 3s infinite; z-index: 0;"></div>
        
        <!-- Content -->
        <div style="position: relative; z-index: 1;">
            <!-- Premium Loading Animation -->
            <div style="margin-bottom: 32px; position: relative;">
                <div style="width: 80px; height: 80px; margin: 0 auto; position: relative;">
                    <!-- Outer Ring -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 4px solid #e5e7eb; border-radius: 50%;"></div>
                    <!-- Animated Ring -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 4px solid transparent; border-top: 4px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <!-- Inner Icon -->
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-credit-card" style="color: white; font-size: 18px;"></i>
                    </div>
                </div>
            </div>

            <!-- Main Title -->
            <h2 style="color: #1f2937; font-size: 28px; font-weight: 700; margin-bottom: 12px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Processando Pagamento
            </h2>
            
            <!-- Subtitle -->
            <p style="color: #6b7280; font-size: 16px; margin-bottom: 32px; line-height: 1.5;">
                Preparando a sua subscrição do <strong id="plan-name" style="color: #3b82f6;">Plano</strong>
            </p>

            <!-- Progress Steps -->
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 32px; gap: 16px;">
                <!-- Step 1 -->
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-check" style="color: white; font-size: 18px;"></i>
                    </div>
                    <span style="font-size: 12px; color: #10b981; font-weight: 600; margin-top: 8px;">Selecionado</span>
                </div>
                
                <!-- Connector -->
                <div style="width: 32px; height: 2px; background: linear-gradient(90deg, #10b981, #3b82f6);"></div>
                
                <!-- Step 2 -->
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); position: relative;">
                        <div style="width: 20px; height: 20px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    </div>
                    <span style="font-size: 12px; color: #3b82f6; font-weight: 600; margin-top: 8px;">Processando</span>
                </div>
                
                <!-- Connector -->
                <div style="width: 32px; height: 2px; background: linear-gradient(90deg, #3b82f6, #d1d5db);"></div>
                
                <!-- Step 3 -->
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 48px; height: 48px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #e5e7eb;">
                        <i class="fas fa-credit-card" style="color: #9ca3af; font-size: 18px;"></i>
                    </div>
                    <span style="font-size: 12px; color: #9ca3af; font-weight: 600; margin-top: 8px;">Pagamento</span>
                </div>
            </div>

            <!-- Plan Card -->
            <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; position: relative; overflow: hidden;">
                <!-- Card Background Pattern -->
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(29, 78, 216, 0.1)); border-radius: 50%;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 id="plan-name-detail" style="color: #1f2937; font-size: 20px; font-weight: 700; margin: 0 0 4px 0;">Plano</h4>
                            <p style="color: #6b7280; font-size: 14px; margin: 0;">Acesso completo ao sistema</p>
                        </div>
                        <div style="text-align: right;">
                            <div id="plan-price" style="color: #3b82f6; font-size: 24px; font-weight: 700; margin: 0;">€0,00</div>
                            <span style="color: #9ca3af; font-size: 12px;">Mensal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Message -->
            <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 1px solid #93c5fd; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; animation: pulse 2s infinite;"></div>
                    <span id="current-status" style="color: #1e40af; font-weight: 600; font-size: 14px;">Criando checkout seguro...</span>
                </div>
            </div>

            <!-- Security Badge -->
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; color: #6b7280; font-size: 13px;">
                <i class="fas fa-shield-alt" style="color: #10b981;"></i>
                <span>Redirecionando para página de pagamento segura</span>
            </div>
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
// Status messages array
const statusMessages = [
    'Criando checkout seguro...',
    'Validando dados do plano...',
    'Preparando gateway de pagamento...',
    'Redirecionando para pagamento...'
];

let currentMessageIndex = 0;
let statusInterval;

// Show loading overlay
function showLoadingOverlay() {
    const overlay = document.getElementById('checkout-loading-overlay');
    overlay.style.display = 'flex';
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.8)';
    overlay.style.zIndex = '9999';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    
    document.body.style.overflow = 'hidden'; // Prevent scrolling
    
    // Start status message rotation
    statusInterval = setInterval(updateStatus, 1500);
}

// Hide loading overlay
function hideLoadingOverlay() {
    document.getElementById('checkout-loading-overlay').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scrolling
    
    if (statusInterval) {
        clearInterval(statusInterval);
    }
}

// Update status message
function updateStatus() {
    const statusElement = document.getElementById('current-status');
    if (statusElement && statusMessages[currentMessageIndex]) {
        statusElement.textContent = statusMessages[currentMessageIndex];
        currentMessageIndex = (currentMessageIndex + 1) % statusMessages.length;
    }
}

// Start checkout process
function startCheckout(planId, planName, planPrice) {
    // Update plan details in overlay
    document.getElementById('plan-name').textContent = planName;
    document.getElementById('plan-name-detail').textContent = planName;
    document.getElementById('plan-price').textContent = '€' + planPrice.toFixed(2).replace('.', ',');
    
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
