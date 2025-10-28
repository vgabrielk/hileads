@extends('layouts.app')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 animate-fadeIn">
    <div class="mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900">Painel Administrativo</h1>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 ml-15">Monitorize sessões Wuzapi e status de utilizadores</p>
                </div>
                <button onclick="location.reload()" class="btn-ripple flex items-center px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Atualizar Status
                </button>
            </div>
        </div>

        <!-- Error Alert -->
        @if($error)
            <div class="mb-8 animate-slideIn">
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-semibold text-red-900">Erro ao carregar dados</p>
                            <p class="text-sm text-red-800 mt-1">{{ $error }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- No Users Warning -->
        @if(count($users) === 0 && !$error)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center empty-state">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-amber-100 to-yellow-100 rounded-3xl mb-6">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Nenhum utilizador ligado</h3>
                <p class="text-gray-600 max-w-md mx-auto">
                    No momento não há utilizadores com sessões ativas na Wuzapi
                </p>
            </div>
        @elseif(count($users) > 0)
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Total de Sessões</h3>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ count($users) }}</p>
                </div>

                <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Ligados</h3>
                        <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">
                        {{ collect($users)->filter(fn($u) => $u['wuzapi']['connected'] ?? false)->count() }}
                    </p>
                </div>

                <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Logados</h3>
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">
                        {{ collect($users)->filter(fn($u) => $u['wuzapi']['loggedIn'] ?? false)->count() }}
                    </p>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Utilizador Laravel
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Verificação
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Status Ligação
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Token
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Laravel User -->
                                    <td class="px-6 py-5">
                                        @if($user['laravel'])
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                                    <span class="text-primary-700 font-bold text-base">{{ substr($user['laravel']->name, 0, 1) }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $user['laravel']->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $user['laravel']->email }}
                                                    </div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1
                                                        @if($user['laravel']->role === 'admin') bg-purple-100 text-purple-700 border border-purple-200
                                                        @else bg-blue-100 text-blue-700 border border-blue-200 @endif">
                                                        {{ $user['laravel']->role === 'admin' ? '👑 Admin' : '👤 User' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic text-sm">Não vinculado</span>
                                        @endif
                                    </td>

                                    <!-- Verification Status -->
                                    <td class="px-6 py-5">
                                        @if(isset($user['wuzapi']['status_code']))
                                            @if($user['wuzapi']['status_code'] === 200)
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Token válido
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    HTTP {{ $user['wuzapi']['status_code'] }}
                                                </span>
                                            @endif
                                        @elseif(isset($user['wuzapi']['error']))
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $user['wuzapi']['error'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">Não verificado</span>
                                        @endif
                                    </td>

                                    <!-- Connection Status -->
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-2">
                                            @if($user['wuzapi']['connected'] ?? false)
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 badge-pulse"></span>
                                                    Ligado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                                    Desconectado
                                                </span>
                                            @endif

                                            @if($user['wuzapi']['loggedIn'] ?? false)
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 badge-pulse"></span>
                                                    Autenticado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                                                    Não autenticado
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Token -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <code class="text-xs bg-gray-100 px-3 py-2 rounded-lg text-gray-700 font-mono border border-gray-200">
                                                {{ substr($user['wuzapi']['token'] ?? '', 0, 20) }}...
                                            </code>
                                            <button 
                                                onclick="copyToClipboard('{{ $user['wuzapi']['token'] ?? '' }}')"
                                                class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all"
                                                title="Copiar token completo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    const icons = {
        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    };
    
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icons[type]}"></path>
            </svg>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/80 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    notification.classList.add(colors[type]);
    document.body.appendChild(notification);
    
    // Animate in
    requestAnimationFrame(() => {
        notification.classList.remove('translate-x-full');
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg animate-slideIn z-50';
        toast.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Token copiado com sucesso!
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }, function(err) {
        console.error('Erro ao copiar: ', err);
        showNotification('Erro ao copiar token. Tente novamente.', 'error');
    });
}
</script>
@endsection
