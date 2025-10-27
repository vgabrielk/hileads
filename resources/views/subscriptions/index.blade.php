@extends('layouts.app')

@section('title', 'Minhas Assinaturas')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Minhas Assinaturas</h1>
        <p class="text-muted-foreground mt-1">Gerencie suas assinaturas e histórico de pagamentos</p>
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
                <p class="text-blue-700 mb-6">Como administrador, você tem acesso completo a todas as funcionalidades do sistema sem necessidade de assinatura.</p>
                <div class="bg-white rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Recursos Disponíveis:</h4>
                    <ul class="text-left text-gray-700 space-y-1">
                        <li>✓ Contatos ilimitados</li>
                        <li>✓ Campanhas ilimitadas</li>
                        <li>✓ Campanhas ilimitadas</li>
                        <li>✓ Acesso a todas as funcionalidades</li>
                        <li>✓ Gerenciamento de usuários</li>
                    </ul>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
    @elseif($subscriptions->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            @foreach($subscriptions as $subscription)
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 gap-3">
                            <div class="flex-1">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900">{{ $subscription->plan->name }}</h3>
                                <p class="text-sm sm:text-base text-gray-600 mt-1">{{ $subscription->plan->description }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-semibold w-fit
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4">
                            <div>
                                <div class="text-xs sm:text-sm text-gray-600">Início</div>
                                <div class="text-sm sm:text-base font-semibold text-gray-900">{{ $subscription->starts_at->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <div class="text-xs sm:text-sm text-gray-600">Expira em</div>
                                <div class="text-sm sm:text-base font-semibold text-gray-900">{{ $subscription->expires_at->format('d/m/Y') }}</div>
                            </div>
                        </div>

                        @if($subscription->status === 'active')
                            <div class="mb-4">
                                <div class="text-xs sm:text-sm text-gray-600 mb-2">Dias restantes</div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ min(100, ($subscription->days_remaining / 30) * 100) }}%"></div>
                                </div>
                                <div class="text-xs sm:text-sm text-gray-600 font-medium">{{ $subscription->days_remaining }} dias</div>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <div class="text-xl sm:text-2xl font-bold text-gray-900">
                                {{ $subscription->plan->formatted_price }}
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                                <a href="{{ route('subscriptions.show', $subscription) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition duration-200 text-center">
                                    Ver Detalhes
                                </a>
                                
                                @if($subscription->status === 'active')
                                    <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" class="w-full sm:w-auto">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition duration-200"
                                                onclick="return handleCancelSubscription(event, 'Tem certeza que deseja cancelar esta assinatura?', 'Esta ação não pode ser desfeita.')">
                                            Cancelar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhuma assinatura encontrada</h3>
            <p class="text-gray-600 mb-6">Você ainda não possui nenhuma assinatura ativa.</p>
            <a href="{{ route('plans.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Ver Planos Disponíveis
            </a>
        </div>
    @endif
</div>
@endsection

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
        // Submit the form
        event.target.submit();
    }
    
    return false; // Prevent default form submission
}
</script>
