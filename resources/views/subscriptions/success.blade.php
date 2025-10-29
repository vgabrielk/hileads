@extends('layouts.app')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 animate-fadeIn">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="flex justify-center mb-6">
                <div class="h-20 w-20 bg-gradient-to-br from-green-400 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">🎉 Assinatura Realizada!</h1>
            <p class="text-lg text-gray-600">O seu pagamento foi processado com sucesso</p>
        </div>

        <!-- Success Card -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="text-green-400 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-900">Assinatura Ativa</h3>
                        @if(isset($subscription) && $subscription)
                            <p class="text-green-700">Plano: {{ $subscription->plan->name }} - {{ $subscription->plan->formatted_price }}</p>
                        @else
                            <p class="text-green-700">A sua assinatura está ativa e pronta para uso!</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Acesso Completo -->
            <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">Acesso Completo</h4>
                        <p class="text-sm text-gray-600">Todas as funcionalidades da plataforma liberadas</p>
                    </div>
                </div>
            </div>

            <!-- Suporte Prioritário -->
            <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">Suporte Prioritário</h4>
                        <p class="text-sm text-gray-600">Atendimento rápido e dedicado para o usuário</p>
                    </div>
                </div>
            </div>

            <!-- Recursos Premium -->
            <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">Recursos Premium</h4>
                        <p class="text-sm text-gray-600">Aceda todas as funcionalidades avançadas</p>
                    </div>
                </div>
            </div>

            <!-- Atualizações Automáticas -->
            <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">Atualizações Automáticas</h4>
                        <p class="text-sm text-gray-600">Sempre com as melhores melhorias e novidades</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Ir para Dashboard
            </a>
            
            <a href="{{ route('subscriptions.index') }}" class="flex items-center justify-center px-6 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 shadow-sm hover:shadow-md transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Ver Minhas Assinaturas
            </a>
        </div>

        <!-- Additional Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <p class="text-sm text-gray-600 mb-2">
                O usuário receberá um e-mail de confirmação em breve.
            </p>
            <p class="text-sm text-gray-500">
                Precisa de ajuda? 
                <a href="mailto:suporte@exemplo.com" class="text-green-600 hover:text-green-700 font-medium transition-colors">
                    Inicie sessão em contato conosco
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Confetti Animation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple confetti effect
    function createConfetti() {
        const colors = ['#10B981', '#059669', '#047857', '#065F46', '#34D399'];
        const confettiCount = 50;
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.top = '-10px';
            confetti.style.width = Math.random() * 10 + 5 + 'px';
            confetti.style.height = confetti.style.width;
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.borderRadius = '50%';
            confetti.style.pointerEvents = 'none';
            confetti.style.zIndex = '9999';
            confetti.style.animation = `confetti-fall ${Math.random() * 3 + 2}s linear forwards`;
            
            document.body.appendChild(confetti);
            
            setTimeout(() => {
                confetti.remove();
            }, 5000);
        }
    }
    
    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Trigger confetti after a short delay
    setTimeout(createConfetti, 500);
});
</script>
@endsection
