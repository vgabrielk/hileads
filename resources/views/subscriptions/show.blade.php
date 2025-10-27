@extends('layouts.app')

@section('title', 'Detalhes da Assinatura')

@section('content')
<div class="p-8 space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar para assinaturas
        </a>
        <h1 class="text-3xl font-bold text-foreground">Detalhes da Assinatura</h1>
        <p class="text-muted-foreground mt-1">Visualize os detalhes da sua assinatura</p>
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

        <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $subscription->plan->name }}</h2>
                        <p class="text-gray-600">{{ $subscription->plan->description }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
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
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informações do Plano -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações do Plano</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Preço:</span>
                                <span class="font-semibold">{{ $subscription->plan->formatted_price }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Período:</span>
                                <span class="font-semibold">{{ $subscription->plan->interval_description }}</span>
                            </div>
                            @if($subscription->plan->max_contacts)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Limite de contatos:</span>
                                    <span class="font-semibold">{{ number_format($subscription->plan->max_contacts) }}</span>
                                </div>
                            @endif
                            @if($subscription->plan->max_campaigns)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Limite de campanhas:</span>
                                    <span class="font-semibold">{{ $subscription->plan->max_campaigns }}</span>
                                </div>
                            @endif
                            @if($subscription->plan->max_mass_sendings)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Limite de envios:</span>
                                    <span class="font-semibold">{{ $subscription->plan->max_mass_sendings }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informações da Assinatura -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações da Assinatura</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Data de início:</span>
                                <span class="font-semibold">{{ $subscription->starts_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Data de expiração:</span>
                                <span class="font-semibold">{{ $subscription->expires_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($subscription->cancelled_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Data de cancelamento:</span>
                                    <span class="font-semibold">{{ $subscription->cancelled_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                            @if($subscription->status === 'active')
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dias restantes:</span>
                                    <span class="font-semibold text-blue-600">{{ $subscription->days_remaining }} dias</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($subscription->plan->features)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recursos Inclusos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($subscription->plan->features as $feature)
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($subscription->status === 'active')
                    <div class="mt-8">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold text-blue-900">Assinatura Ativa</div>
                                    <div class="text-blue-700 text-sm">Sua assinatura está ativa e você tem acesso a todos os recursos do plano.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex justify-between items-center">
                    <div>
                        @if($subscription->status === 'active')
                            <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                                        onclick="return handleCancelSubscription(event, 'Tem certeza que deseja cancelar esta assinatura?', 'Esta ação não pode ser desfeita.')">
                                    Cancelar Assinatura
                                </button>
                            </form>
                        @elseif($subscription->status === 'cancelled' || $subscription->status === 'expired')
                            <a href="{{ route('plans.index') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Assinar Novamente
                            </a>
                        @endif
                    </div>
                    
                    <div class="text-sm text-gray-500">
                        Criada em {{ $subscription->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
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
