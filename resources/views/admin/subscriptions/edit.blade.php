@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Editar Subscrição</h1>
            <p class="text-muted-foreground mt-1">Edite as informações desta subscrição</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.subscriptions.show', $subscription) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="space-y-4 sm:space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-foreground mb-2">Status</label>
                            <select name="status" id="status" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status', $subscription->status) == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-foreground mb-2">Plano</label>
                            <select name="plan_id" id="plan_id" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id', $subscription->plan_id) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} - €{{ number_format($plan->price, 2, ',', '.') }}/{{ $plan->interval }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-foreground mb-2">Data de Início</label>
                            <input type="datetime-local" name="starts_at" id="starts_at" 
                                   value="{{ old('starts_at', $subscription->starts_at->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('starts_at')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-foreground mb-2">Data de Expiração</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" 
                                   value="{{ old('expires_at', $subscription->expires_at->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('expires_at')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Observações</h3>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-foreground mb-2">Notas Administrativas</label>
                        <textarea name="notes" id="notes" rows="4" 
                                  placeholder="Adicione observações sobre esta subscrição..."
                                  class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('notes', $subscription->notes) }}</textarea>
                        @error('notes')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salvar Alterações
                    </button>
                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Current Subscription Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Atuais</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="font-medium text-muted-foreground">Utilizador:</span>
                        <p class="text-foreground">{{ $subscription->user->name }}</p>
                        <p class="text-muted-foreground">{{ $subscription->user->email }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-muted-foreground">Plano Atual:</span>
                        <p class="text-foreground">{{ $subscription->plan->name }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-muted-foreground">Status Atual:</span>
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
                        <span class="font-medium text-muted-foreground">Valor Atual:</span>
                        <p class="text-foreground">€{{ number_format($subscription->amount ?? $subscription->plan->price, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Stripe Info -->
            @if($subscription->stripe_subscription_id)
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Stripe</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="font-medium text-muted-foreground">ID da Subscrição:</span>
                        <p class="text-foreground font-mono text-xs">{{ $subscription->stripe_subscription_id }}</p>
                    </div>
                    @if($subscription->stripe_customer_id)
                    <div>
                        <span class="font-medium text-muted-foreground">ID do Cliente:</span>
                        <p class="text-foreground font-mono text-xs">{{ $subscription->stripe_customer_id }}</p>
                    </div>
                    @endif
                    <div class="mt-3 p-3 bg-warning/10 rounded-lg">
                        <p class="text-warning text-xs">
                            <strong>Atenção:</strong> Alterações no status ou plano podem sincronizar automaticamente com o Stripe.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Help -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ajuda</h3>
                <div class="space-y-2 text-sm text-muted-foreground">
                    <p><strong>Status:</strong> Define o estado atual da subscrição.</p>
                    <p><strong>Plano:</strong> Altera o plano da subscrição e recalcula o valor.</p>
                    <p><strong>Datas:</strong> Ajuste as datas de início e expiração.</p>
                    <p><strong>Observações:</strong> Adicione notas administrativas sobre a subscrição.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-update expires_at when plan changes
document.getElementById('plan_id').addEventListener('change', function() {
    const planId = this.value;
    const startsAt = document.getElementById('starts_at').value;
    
    if (startsAt) {
        // This would need to be implemented with AJAX to get plan details
        // For now, we'll just show a message
        console.log('Plan changed, you may need to adjust the expiration date');
    }
});

// Validate that expires_at is after starts_at
document.getElementById('expires_at').addEventListener('change', function() {
    const startsAt = document.getElementById('starts_at').value;
    const expiresAt = this.value;
    
    if (startsAt && expiresAt && new Date(expiresAt) <= new Date(startsAt)) {
        alert('A data de expiração deve ser posterior à data de início.');
        this.value = '';
    }
});
</script>
@endsection
