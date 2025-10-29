@extends('layouts.app')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 animate-fadeIn">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar para conexões
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Conectar WhatsApp</h1>
            <p class="mt-2 text-sm text-gray-600">Escaneie o Código QR com o seu WhatsApp para estabelecer conexão</p>
        </div>

        <!-- QR Code Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($qrCode)
                <div class="p-10 text-center">
                    <!-- Icon -->
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-3xl mb-6 animate-pulse">
                        <svg class="w-10 h-10 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Escaneie o Código QR</h2>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        Utilize o seu smartphone para digitalizar o código e conectar o seu WhatsApp à plataforma
                    </p>

                    <!-- QR Code -->
                    <div class="inline-block p-8 bg-gradient-to-br from-gray-50 to-gray-100 rounded-3xl border-2 border-gray-200 shadow-xl mb-8">
                        <img src="{{ $qrCode }}" alt="QR Code WhatsApp" class="w-72 h-72 mx-auto rounded-xl">
                    </div>

                    <!-- Instructions -->
                    <div class="max-w-2xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Step 1 -->
                            <div class="bg-gradient-to-br from-primary-50 to-green-50 rounded-2xl p-6 border border-primary-100">
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm mx-auto">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2">1. Abra o WhatsApp</h3>
                                <p class="text-sm text-gray-600">No o seu smartphone, abra a aplicação WhatsApp</p>
                            </div>

                            <!-- Step 2 -->
                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100">
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm mx-auto">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2">2. Configurações</h3>
                                <p class="text-sm text-gray-600">Aceda a Menu > Dispositivos conectados</p>
                            </div>

                            <!-- Step 3 -->
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm mx-auto">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2">3. Escaneie o Código</h3>
                                <p class="text-sm text-gray-600">Aponte a câmara para o Código QR acima</p>
                            </div>
                        </div>
                    </div>

                    <!-- Loading indicator -->
                    <div id="loading-indicator" class="mt-8 flex items-center justify-center space-x-2 text-sm text-gray-600">
                        <div class="spinner"></div>
                        <span>A aguardar conexão...</span>
                    </div>

                    <!-- Success indicator -->
                    <div id="success-indicator" class="mt-8 hidden">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-green-800 mb-2">Conectado com sucesso!</h3>
                        <p class="text-green-600 mb-4">O seu WhatsApp foi conectado à plataforma.</p>
                        <a href="{{ route('whatsapp.index') }}" class="btn-ripple inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-sm transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Continuar
                        </a>
                    </div>
                </div>
            @else
                <!-- Error State -->
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-3xl mb-6">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Não foi possível gerar o QR Code</h3>
                    <p class="text-gray-600 mb-6">Houve um problema ao tentar estabelecer a conexão. Tente novamente.</p>
                    <a href="{{ route('whatsapp.index') }}" class="btn-ripple inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-sm transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Voltar e tentar novamente
                    </a>
                </div>
            @endif
        </div>

        <!-- Info Card -->
        <div class="mt-6 bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-semibold text-blue-900 mb-1">Dica de segurança</h3>
                    <p class="text-sm text-blue-800">
                        Este QR Code é único e seguro. Nunca compartilhe com terceiros. A conexão será estabelecida diretamente entre o seu WhatsApp e nossa plataforma.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingIndicator = document.getElementById('loading-indicator');
    const successIndicator = document.getElementById('success-indicator');
    
    if (loadingIndicator && successIndicator) {
        // Verificar status a cada 3 segundos
        const checkStatus = setInterval(async function() {
            try {
                const response = await fetch('{{ route("whatsapp.status") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.logged_in) {
                    // Usuário logado com sucesso
                    loadingIndicator.style.display = 'none';
                    successIndicator.classList.remove('hidden');
                    clearInterval(checkStatus);
                } else if (data.success && data.connected && !data.logged_in) {
                    // Conectado mas ainda não logado - continuar aguardando
                    console.log('Conectado, aguardando login...');
                } else if (!data.success) {
                    // Erro na verificação
                    console.error('Erro ao verificar status:', data.message);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
            }
        }, 3000);
        
        // Limpar o intervalo após 5 minutos para evitar requisições infinitas
        setTimeout(() => {
            clearInterval(checkStatus);
        }, 300000); // 5 minutos
    }
});
</script>
@endpush
@endsection
