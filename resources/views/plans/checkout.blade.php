@extends('layouts.app')

@section('title', 'Checkout - ' . $plan->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header da página -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Finalizar Assinatura</h2>
                    <p class="text-muted mb-0">Complete o seu pagamento para ativar o plano <strong>{{ $plan->name }}</strong></p>
                </div>
                <div class="text-end">
                    <div class="badge bg-primary fs-6">R$ {{ number_format($plan->price, 2, ',', '.') }}</div>
                    <div class="text-muted small">{{ ucfirst($plan->interval) }}</div>
                </div>
            </div>

            <!-- Informações do plano -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $plan->name }}</h5>
                            <p class="card-text text-muted">{{ $plan->description }}</p>
                            
                            @if($plan->features)
                                <div class="mt-3">
                                    <h6 class="fw-bold">Recursos incluídos:</h6>
                                    <ul class="list-unstyled">
                                        @foreach($plan->features as $feature)
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success me-2"></i>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold text-primary">R$ {{ number_format($plan->price, 2, ',', '.') }}</div>
                            <div class="text-muted">por {{ $plan->interval === 'monthly' ? 'mês' : 'ano' }}</div>
                            <hr>
                            <div class="small text-muted">
                                <div>Checkout ID: <code>{{ $checkoutData['id'] ?? 'N/A' }}</code></div>
                                <div>Status: <span class="badge bg-warning">Pendente</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Iframe do checkout -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Pagamento Seguro
                                </h5>
                                <div class="d-flex align-items-center">
                                    <img src="https://img.shields.io/badge/Seguro-Bestfy-green" alt="Seguro" class="me-2">
                                    <small class="text-muted">Processado pela Bestfy</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if(isset($checkoutData['secureUrl']) && $checkoutData['secureUrl'])
                                <!-- Loading spinner -->
                                <div id="checkout-loading" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Carregando página de pagamento...</p>
                                </div>

                                <!-- Iframe do checkout -->
                                <iframe 
                                    id="checkout-iframe"
                                    src="{{ $checkoutData['secureUrl'] }}"
                                    width="100%" 
                                    height="800"
                                    frameborder="0"
                                    style="display: none;"
                                    onload="hideLoading()"
                                    sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-top-navigation"
                                ></iframe>

                                <!-- Fallback se o iframe não carregar -->
                                <div id="checkout-fallback" style="display: none;" class="text-center py-5">
                                    <div class="alert alert-warning">
                                        <h5>Não foi possível carregar o checkout</h5>
                                        <p>Clique no botão abaixo para abrir em uma nova aba:</p>
                                        <a href="{{ $checkoutData['secureUrl'] }}" target="_blank" class="btn btn-primary">
                                            <i class="fas fa-external-link-alt me-2"></i>
                                            Abrir Checkout
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="alert alert-danger">
                                        <h5>Erro ao carregar checkout</h5>
                                        <p>Não foi possível gerar a URL de pagamento. Tente novamente.</p>
                                        <a href="{{ route('plans.show', $plan) }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Voltar ao Plano
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações de segurança -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-shield-alt text-success fs-1 mb-3"></i>
                                    <h6>Pagamento Seguro</h6>
                                    <p class="small text-muted">Seus dados são protegidos com encriptação SSL</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-lock text-success fs-1 mb-3"></i>
                                    <h6>Dados Protegidos</h6>
                                    <p class="small text-muted">Não armazenamos informações de cartão</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-clock text-success fs-1 mb-3"></i>
                                    <h6>Ativação Imediata</h6>
                                    <p class="small text-muted">Acesso liberado instantaneamente após pagamento</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status do pagamento (hidden por padrão) -->
            <div id="payment-status" class="mt-4" style="display: none;">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle me-2"></i>Status do Pagamento</h5>
                    <p class="mb-0">Aguardando confirmação do pagamento...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Esconder loading e mostrar iframe
function hideLoading() {
    document.getElementById('checkout-loading').style.display = 'none';
    document.getElementById('checkout-iframe').style.display = 'block';
}

// Verificar se o iframe carregou corretamente
setTimeout(function() {
    const iframe = document.getElementById('checkout-iframe');
    const loading = document.getElementById('checkout-loading');
    const fallback = document.getElementById('checkout-fallback');
    
    if (loading && loading.style.display !== 'none') {
        // Se ainda está carregando após 10 segundos, mostrar fallback
        loading.style.display = 'none';
        fallback.style.display = 'block';
    }
}, 10000);

// Escutar mensagens do iframe (para comunicação com o checkout)
window.addEventListener('message', function(event) {
    // Verificar origem por segurança
    if (event.origin !== 'https://checkout.bestfybr.com.br') {
        return;
    }
    
    console.log('Mensagem recebida do checkout:', event.data);
    
    // Processar diferentes tipos de mensagem
    if (event.data.type === 'payment_success') {
        showPaymentSuccess();
    } else if (event.data.type === 'payment_error') {
        showPaymentError(event.data.message);
    }
});

function showPaymentSuccess() {
    const statusDiv = document.getElementById('payment-status');
    statusDiv.innerHTML = `
        <div class="alert alert-success">
            <h5><i class="fas fa-check-circle me-2"></i>Pagamento Confirmado!</h5>
            <p class="mb-0">A sua assinatura foi ativada com sucesso. Redirecionando...</p>
        </div>
    `;
    statusDiv.style.display = 'block';
    
    // Redirecionar após 3 segundos
    setTimeout(function() {
        window.location.href = '{{ route("subscriptions.index") }}';
    }, 3000);
}

function showPaymentError(message) {
    const statusDiv = document.getElementById('payment-status');
    statusDiv.innerHTML = `
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Erro no Pagamento</h5>
            <p class="mb-0">${message || 'Ocorreu um erro durante o pagamento. Tente novamente.'}</p>
        </div>
    `;
    statusDiv.style.display = 'block';
}

// Verificar status do pagamento periodicamente
let statusCheckInterval = setInterval(function() {
    fetch('{{ route("subscriptions.status-check") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Verificar se a assinatura foi ativada
        if (data.has_subscription && data.subscription && data.subscription.status === 'active') {
            clearInterval(statusCheckInterval);
            showPaymentSuccess();
        }
    })
    .catch(error => {
        console.log('Erro ao verificar status:', error);
    });
}, 5000); // Verificar a cada 5 segundos

// Limpar intervalo após 10 minutos
setTimeout(function() {
    clearInterval(statusCheckInterval);
}, 600000);
</script>
@endsection
