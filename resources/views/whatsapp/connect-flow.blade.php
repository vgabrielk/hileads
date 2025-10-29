@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-3xl font-bold text-foreground mb-2">Conectar WhatsApp</h1>
        <p class="text-muted-foreground">Siga os passos abaixo para conectar a sua conta WhatsApp</p>
    </div>

    <!-- Connection Flow Steps -->
    <div class="max-w-4xl mx-auto">
        <!-- Step 1: Connect to WhatsApp -->
        <div class="bg-card rounded-lg border border-border p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-primary-foreground font-bold text-sm">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-foreground">Conectar ao WhatsApp</h3>
                </div>
                <div id="connect-status" class="hidden">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success/10 text-success">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Conectado
                    </span>
                </div>
            </div>
            
            <p class="text-muted-foreground mb-4">
                Clique no botão abaixo para iniciar a conexão com os servidores do WhatsApp.
            </p>
            
            <button id="connect-btn" 
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                </svg>
                <span id="connect-text">Conectar ao WhatsApp</span>
                <svg id="connect-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>

        <!-- Step 2: Get QR Code -->
        <div class="bg-card rounded-lg border border-border p-6 mb-6 hidden" id="qr-step">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-primary-foreground font-bold text-sm">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-foreground">Obter QR Code</h3>
                </div>
                <div id="qr-status" class="hidden">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success/10 text-success">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        QR Code Gerado
                    </span>
                </div>
            </div>
            
            <p class="text-muted-foreground mb-4">
                Após ligar, clique no botão abaixo para obter o QR Code que será escaneado no seu telemóvel.
            </p>
            
            <button id="qr-btn" 
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-secondary-foreground bg-secondary hover:bg-secondary/90 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span id="qr-text">Obter QR Code</span>
                <svg id="qr-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>

        <!-- Step 3: QR Code Display -->
        <div id="qr-display" class="bg-card rounded-lg border border-border p-8 text-center hidden">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-success/10 rounded-lg mb-6 animate-pulse">
                <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-foreground mb-2">Escaneie o QR Code</h3>
            <p class="text-muted-foreground mb-8">Abra o WhatsApp no seu telemóvel e escaneie o código abaixo</p>
            <div class="inline-block p-6 bg-background rounded-lg border-2 border-border shadow-lg">
                <img id="qr-image" src="" alt="QR Code WhatsApp" class="w-64 h-64 mx-auto">
            </div>
            <div class="mt-8 flex items-center justify-center space-x-4">
                <div class="flex items-center text-sm text-muted-foreground">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>1. Abra o WhatsApp</span>
                </div>
                <div class="flex items-center text-sm text-muted-foreground">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <span>2. Toque em Configurações</span>
                </div>
                <div class="flex items-center text-sm text-muted-foreground">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>3. Escaneie o QR Code</span>
                </div>
            </div>
        </div>

        <!-- Step 4: Status Check -->
        <div id="status-check" class="bg-card rounded-lg border border-border p-6 hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-primary-foreground font-bold text-sm">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-foreground">Verificar Status</h3>
                </div>
                <div id="final-status" class="hidden">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success/10 text-success">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Conectado e Logado
                    </span>
                </div>
            </div>
            
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-lg mb-4 animate-pulse">
                    <svg class="w-8 h-8 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <p class="text-muted-foreground mb-4">A aguardar a digitalização o QR Code...</p>
                <p class="text-sm text-muted-foreground">Verificando status automaticamente...</p>
            </div>
        </div>

        <!-- Success Message -->
        <div id="success-message" class="bg-success/10 border border-success/20 rounded-lg p-6 text-center hidden">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-success rounded-lg mb-4">
                <svg class="w-8 h-8 text-success-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-success mb-2">Conectado com Sucesso!</h3>
            <p class="text-muted-foreground mb-6">A sua conta WhatsApp foi conectada e está pronta para uso.</p>
            <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar para Conexões
            </a>
        </div>

        <!-- Error Message -->
        <div id="error-message" class="bg-destructive/10 border border-destructive/20 rounded-lg p-6 text-center hidden">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-destructive rounded-lg mb-4">
                <svg class="w-8 h-8 text-destructive-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-destructive mb-2">Erro na Conexão</h3>
            <p id="error-text" class="text-muted-foreground mb-6">Ocorreu um erro durante o processo de conexão.</p>
            <button onclick="location.reload()" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Tentar Novamente
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const connectBtn = document.getElementById('connect-btn');
    const qrBtn = document.getElementById('qr-btn');
    const qrDisplay = document.getElementById('qr-display');
    const statusCheck = document.getElementById('status-check');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const qrStep = document.getElementById('qr-step');
    
    let isConnected = false;
    let statusCheckInterval = null;

    // Connect to WhatsApp
    connectBtn.addEventListener('click', async function() {
        setButtonLoading(connectBtn, true);
        
        try {
            const response = await fetch('{{ route("whatsapp.connect-session") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                isConnected = true;
                showStepStatus('connect-status');
                enableButton(qrBtn);
                showMessage('success', 'Conectado ao WhatsApp com sucesso!');
                qrStep.classList.remove('hidden');
            } else {
                throw new Error(result.message || 'Erro ao ligar');
            }
        } catch (error) {
            showError('Erro ao ligar: ' + error.message);
        } finally {
            setButtonLoading(connectBtn, false);
        }
    });

    // Get QR Code
    qrBtn.addEventListener('click', async function() {
        setButtonLoading(qrBtn, true);
        
        try {
            const response = await fetch('{{ route("whatsapp.get-qr") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success && result.data.QRCode) {
                showQRCode(result.data.QRCode);
                showStepStatus('qr-status');
                startStatusCheck();
            } else {
                throw new Error(result.message || 'Erro ao obter QR Code');
            }
        } catch (error) {
            showError('Erro ao obter QR Code: ' + error.message);
        } finally {
            setButtonLoading(qrBtn, false);
        }
    });

    function showQRCode(qrCodeData) {
        const qrImage = document.getElementById('qr-image');
        qrImage.src = qrCodeData;
        qrDisplay.classList.remove('hidden');
        statusCheck.classList.remove('hidden');
    }

    function startStatusCheck() {
        statusCheckInterval = setInterval(async () => {
            try {
                const response = await fetch('{{ route("whatsapp.check-status") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data.LoggedIn) {
                    clearInterval(statusCheckInterval);
                    showSuccess();
                }
            } catch (error) {
                console.error('Erro ao verificar status:', error);
            }
        }, 3000); // Check every 3 seconds
    }

    function showSuccess() {
        statusCheck.classList.add('hidden');
        qrDisplay.classList.add('hidden');
        successMessage.classList.remove('hidden');
        showStepStatus('final-status');
    }

    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
        qrDisplay.classList.add('hidden');
        statusCheck.classList.add('hidden');
    }

    function showMessage(type, message) {
        // You can implement a toast notification here
        console.log(type + ':', message);
    }

    function setButtonLoading(button, loading) {
        const text = button.querySelector('span');
        const spinner = button.querySelector('svg:last-child');
        
        if (loading) {
            button.disabled = true;
            text.classList.add('hidden');
            spinner.classList.remove('hidden');
        } else {
            button.disabled = false;
            text.classList.remove('hidden');
            spinner.classList.add('hidden');
        }
    }

    function enableButton(button) {
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    function showStepStatus(statusElementId) {
        const statusElement = document.getElementById(statusElementId);
        if (statusElement) {
            statusElement.classList.remove('hidden');
        }
    }

    // Clean up interval on page unload
    window.addEventListener('beforeunload', function() {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
        }
    });
});
</script>
@endsection
