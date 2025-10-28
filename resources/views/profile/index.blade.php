@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-foreground">Meu Perfil</h1>
        <p class="text-muted-foreground mt-1">Faça a gestão suas informações pessoais e configurações da conta</p>
    </div>

                <!-- User Information -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h2 class="text-lg font-semibold text-foreground">Informações Pessoais</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Nome Completo</label>
                    <p class="text-base text-foreground font-medium">{{ $user->name }}</p>
                        </div>
                        <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Email</label>
                    <p class="text-base text-foreground font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Função</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($user->role === 'admin') bg-primary/10 text-primary 
                                    @else bg-secondary text-secondary-foreground @endif">
                        @if($user->role === 'admin')
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Administrador
                        @else
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Utilizador
                        @endif
                                </span>
                        </div>
                        <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Membro desde</label>
                    <p class="text-base text-foreground font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
                        </div>
                    </div>
                </div>

    <!-- Subscription Information -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h2 class="text-lg font-semibold text-foreground">Informações da Subscrição</h2>
        </div>
        <div class="p-6">
            @if($user->activeSubscription)
                <div class="space-y-6">
                    <!-- Current Plan -->
                    <div class="flex items-center justify-between p-4 bg-primary/5 border border-primary/20 rounded-lg">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-foreground">{{ $user->activeSubscription->plan->name }}</h3>
                                <p class="text-sm text-muted-foreground">{{ $user->activeSubscription->plan->description }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-primary">{{ $user->activeSubscription->plan->formatted_price }}</p>
                            <p class="text-sm text-muted-foreground">{{ $user->activeSubscription->plan->interval_description }}</p>
                        </div>
                    </div>
                    
                    <!-- Plan Features -->
                    <div>
                        <h4 class="text-sm font-semibold text-foreground mb-3">Recursos Incluídos</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @if($user->activeSubscription->plan->max_contacts)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-foreground">{{ number_format($user->activeSubscription->plan->max_contacts) }} contactos</span>
                                </div>
                            @endif
                            @if($user->activeSubscription->plan->max_campaigns)
                                @php
                                    $campaignStats = \App\Helpers\PlanLimitsHelper::getPlanUsageStats($user);
                                    $campaignsUsed = $campaignStats['campaigns']['current'];
                                    $campaignsMax = $campaignStats['campaigns']['max'];
                                    $campaignsRemaining = $campaignStats['campaigns']['remaining'];
                                @endphp
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-sm text-foreground">{{ number_format($campaignsMax) }} campanhas</span>
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ $campaignsUsed }}/{{ $campaignsMax }} usadas
                                        @if($campaignsRemaining > 0)
                                            ({{ $campaignsRemaining }} restantes)
                                        @else
                                            <span class="text-destructive font-semibold">(Limite atingido)</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($user->activeSubscription->plan->max_mass_sendings)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-foreground">{{ number_format($user->activeSubscription->plan->max_mass_sendings) }} campanhas</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm text-foreground">Suporte por e-mail</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-border">
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success/10 text-success">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Ativa
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Próxima Cobrança</label>
                            <p class="text-sm text-foreground">{{ $user->activeSubscription->expires_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Método de Pagamento</label>
                            <p class="text-sm text-foreground">{{ $user->activeSubscription->payment_method ?? 'Não informado' }}</p>
                                </div>
                            </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-4 border-t border-border">
                        <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-foreground hover:bg-accent rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Subscrições
                        </a>
                        <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Alterar Plano
                        </a>
                    </div>
                                </div>
                        @else
                            <div class="text-center py-8">
                    <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-foreground mb-2">Nenhuma subscrição ativa</h3>
                    <p class="text-muted-foreground mb-6">Escolha um plano para começar a usar todas as funcionalidades do HiLeads.</p>
                    <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Escolher Plano
                    </a>
                            </div>
                        @endif
                    </div>
                </div>

    <!-- API Configuration -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h2 class="text-lg font-semibold text-foreground">Configurações da API</h2>
            <p class="text-sm text-muted-foreground mt-1">Faça a gestão o seu token de API do WhatsApp</p>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Current Token -->
                <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Token de API Atual</label>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 bg-muted/50 border border-input rounded-lg px-3 py-2 font-mono text-sm">
                            <span id="api-token" class="text-foreground">{{ $user->api_token }}</span>
                        </div>
                        <button 
                            onclick="copyToken()" 
                            class="px-3 py-2 text-sm font-medium text-muted-foreground hover:text-foreground border border-input rounded-lg hover:bg-accent transition-colors"
                            title="Copiar token">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-muted-foreground mt-2">
                        Este token é usado para ligar a sua conta ao WhatsApp via Wuzapi
                    </p>
                </div>

                <!-- Token Actions -->
                <div class="flex items-center gap-3">
                    <button 
                        onclick="regenerateToken()" 
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Regenerar Token
                    </button>
                    
                    <button 
                        onclick="showTokenInfo()" 
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-secondary-foreground bg-secondary hover:bg-secondary/90 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Como Usar
                    </button>
                </div>

                <!-- Warning -->
                <div class="bg-warning/10 border border-warning/20 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-warning mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-warning mb-1">Atenção</h4>
                            <p class="text-sm text-warning/80">
                                Regenerar o token irá desconectar todas as sessões WhatsApp ativas. 
                                Precisará reconectar o WhatsApp após regenerar o token.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
                            </div>
                        </div>

    <!-- Account Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-card rounded-lg border border-border p-6">
            <div class="flex items-center justify-between">
                        <div>
                    <p class="text-sm font-medium text-muted-foreground">Total de Grupos</p>
                    <p class="text-2xl font-bold text-foreground">{{ $user->groups()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                            </div>
                        </div>

        <div class="bg-card rounded-lg border border-border p-6">
            <div class="flex items-center justify-between">
                        <div>
                    <p class="text-sm font-medium text-muted-foreground">Campanhas</p>
                    <p class="text-2xl font-bold text-foreground">{{ $user->massSendings()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                            </div>
                        </div>
                    </div>

        <div class="bg-card rounded-lg border border-border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total de Contactos</p>
                    <p class="text-2xl font-bold text-foreground">{{ $user->groups()->sum('contacts_count') }}</p>
                    </div>
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Função para copiar token
function copyToken() {
    const tokenElement = document.getElementById('api-token');
    const token = tokenElement.textContent;
    
    navigator.clipboard.writeText(token).then(() => {
        showNotification('Token copiado para a área de transferência!', 'success');
    }).catch(err => {
        console.error('Erro ao copiar token:', err);
        showNotification('Erro ao copiar token. Tente novamente.', 'error');
    });
}

// Função para regenerar token
async function regenerateToken() {
    if (!confirm('Tem certeza que deseja regenerar o token? Isso irá desconectar todas as sessões WhatsApp ativas e precisará reconectar.')) {
        return;
    }
    
        const button = event.target;
    const originalText = button.innerHTML;
    
    // Mostrar loading
    button.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Regenerando...
    `;
    button.disabled = true;
    
    try {
        const response = await fetch('{{ route("profile.regenerate-token") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                // Atualizar o token na interface
                document.getElementById('api-token').textContent = data.newToken;
                showNotification('Token regenerado com sucesso!', 'success');
            } else {
                showNotification('Erro ao regenerar token: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        } else {
            showNotification('Erro de ligação. Tente novamente.', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro de ligação. Tente novamente.', 'error');
    } finally {
        // Restaurar botão
        button.innerHTML = originalText;
        button.disabled = false;
    }
}

// Função para mostrar informações sobre o token
function showTokenInfo() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-card rounded-lg border border-border max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-foreground">Como Usar o Token de API</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-muted-foreground hover:text-foreground">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <h4 class="font-semibold text-foreground mb-2">O que é o Token de API?</h4>
                    <p class="text-sm text-muted-foreground">
                        O token de API é uma chave única que permite que a sua conta se conecte ao WhatsApp através da plataforma Wuzapi. 
                        É como uma palavra-passe especial que autentica suas requisições.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-foreground mb-2">Como funciona?</h4>
                    <ol class="text-sm text-muted-foreground space-y-1 list-decimal list-inside">
                        <li>O utilizador conecta o seu WhatsApp usando o QR Code</li>
                        <li>O sistema gera um token único para a sua conta</li>
                        <li>Este token é usado para enviar mensagens e gerir grupos</li>
                        <li>Se o token for regenerado, precisa reconectar o WhatsApp</li>
                    </ol>
                </div>
                
                <div>
                    <h4 class="font-semibold text-foreground mb-2">Quando regenerar?</h4>
                    <ul class="text-sm text-muted-foreground space-y-1 list-disc list-inside">
                        <li>Se o utilizador suspeitar que o token foi comprometido</li>
                        <li>Se estiver tendo problemas de ligação</li>
                        <li>Se quiser desconectar todas as sessões ativas</li>
                        <li>Como medida de segurança periódica</li>
                    </ul>
                </div>
                
                <div class="bg-warning/10 border border-warning/20 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-warning mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-warning mb-1">Importante</h4>
                            <p class="text-sm text-warning/80">
                                Mantenha o seu token seguro e não compartilhe com terceiros. 
                                Regenerar o token desconectará todas as sessões WhatsApp ativas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-border flex justify-end">
                <button 
                    onclick="this.closest('.fixed').remove()" 
                    class="px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    Entendi
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Função para mostrar notificações
function showNotification(message, type = 'info') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-success text-success-foreground' :
        type === 'error' ? 'bg-destructive text-destructive-foreground' :
        'bg-primary text-primary-foreground'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remover após 5 segundos
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Verificar se há mensagens de sucesso do servidor
@if(session('success'))
    showNotification('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    showNotification('{{ session('error') }}', 'error');
@endif
</script>

@endsection

