@extends('layouts.app')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 animate-fadeIn">
    <div class="mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('mass-sendings.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar para envio em massas
            </a>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-4">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $massSending->name }}</h1>
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold
                            @if($massSending->status === 'draft') bg-gray-100 text-gray-700 border border-gray-200
                            @elseif($massSending->status === 'active') bg-green-100 text-green-700 border border-green-200
                            @elseif($massSending->status === 'paused') bg-amber-100 text-amber-700 border border-amber-200
                            @elseif($massSending->status === 'failed') bg-red-100 text-red-700 border border-red-200
                            @else bg-blue-100 text-blue-700 border border-blue-200 @endif">
                            <span class="w-2 h-2 rounded-full mr-2
                                @if($massSending->status === 'draft') bg-gray-500
                                @elseif($massSending->status === 'active') bg-green-500 badge-pulse
                                @elseif($massSending->status === 'paused') bg-amber-500
                                @elseif($massSending->status === 'failed') bg-red-500
                                @else bg-blue-500 @endif">
                            </span>
                            @if($massSending->status === 'draft') Rascunho
                            @elseif($massSending->status === 'active') Ativa
                            @elseif($massSending->status === 'paused') Pausada
                            @elseif($massSending->status === 'completed') Concluída
                            @elseif($massSending->status === 'failed') Falhou
                            @else {{ ucfirst($massSending->status) }}
                            @endif
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">Acompanhe o desempenho e faça a gestão a sua envio em massa</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if($massSending->status === 'draft')
                        <form method="POST" action="{{ route('mass-sendings.start', $massSending) }}" class="inline">
                            @csrf
                            <button type="submit" id="start-mass-sending-btn" class="btn-ripple inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-sm transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Iniciar Envio em massa
                            </button>
                        </form>
                        
                        <!-- Toast Notification -->
                        <div id="toast-notification" class="fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span id="toast-message">Envio em massa iniciada! Processando em segundo plano...</span>
                            </div>
                        </div>

                        <!-- Success Notification -->
                        <div id="success-notification" class="fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span id="success-message">Envio em massa concluída com sucesso!</span>
                            </div>
                        </div>

                        <!-- Progress Modal -->
                        <div id="progress-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-primary-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Enviando Mensagens</h3>
                                        <p class="text-sm text-gray-600 mb-4" id="progress-message">Iniciando envio das mensagens...</p>
                                        
                                        <!-- Progress Bar -->
                                        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
                                        </div>
                                        
                                        <!-- Progress Stats -->
                                        <div class="grid grid-cols-3 gap-4 text-center">
                                            <div>
                                                <div class="text-2xl font-bold text-primary-600" id="progress-sent">0</div>
                                                <div class="text-xs text-gray-500">Enviadas</div>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-red-600" id="progress-failed">0</div>
                                                <div class="text-xs text-gray-500">Falharam</div>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-gray-600" id="progress-total">0</div>
                                                <div class="text-xs text-gray-500">Total</div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex gap-2 mt-4">
                                            <button id="close-progress" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                                Fechar
                                            </button>
                                            <button id="stop-polling" class="px-4 py-2 bg-red-200 text-red-700 rounded-lg hover:bg-red-300 transition-colors">
                                                Parar Atualização
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    @elseif($massSending->status === 'completed')
                        @php
                            $remainingContacts = $massSending->total_contacts - $massSending->sent_count;
                        @endphp
                        @if($remainingContacts > 0)
                            <form method="POST" action="{{ route('mass-sendings.resume', $massSending) }}" class="inline">
                                @csrf
                                <button type="submit" id="resume-mass-sending-btn" class="btn-ripple inline-flex items-center px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-xl shadow-sm transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Retomar Campanha ({{ $remainingContacts }} restantes)
                                </button>
                            </form>
                        @else
                            <div class="inline-flex items-center px-4 py-2.5 bg-green-100 text-green-800 font-medium rounded-xl">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Envio em massa Concluída
                            </div>
                        @endif
                    @elseif($massSending->status === 'failed')
                        @php
                            $remainingContacts = $massSending->total_contacts - $massSending->sent_count;
                        @endphp
                        @if($remainingContacts > 0)
                            <form method="POST" action="{{ route('mass-sendings.resume', $massSending) }}" class="inline">
                                @csrf
                                <button type="submit" id="resume-failed-mass-sending-btn" class="btn-ripple inline-flex items-center px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-xl shadow-sm transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Retomar Campanha ({{ $remainingContacts }} restantes)
                                </button>
                            </form>
                        @else
                            <div class="inline-flex items-center px-4 py-2.5 bg-red-100 text-red-800 font-medium rounded-xl">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                Campanha Falhou
                            </div>
                        @endif
                    @elseif($massSending->status === 'paused')
                        <form method="POST" action="{{ route('mass-sendings.resume', $massSending) }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-ripple inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-sm transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Retomar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mass Sending Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Total Contacts -->
            <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Total de Contatos</h3>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($massSending->total_contacts) }}</p>
                <p class="text-xs text-gray-500 mt-2">Contatos selecionados</p>
            </div>

            <!-- Sent Count -->
            <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Enviados</h3>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($massSending->sent_count) }}</p>
                <p class="text-xs text-green-600 mt-2 flex items-center">
                    @php
                        $percentage = $massSending->total_contacts > 0 ? ($massSending->sent_count / $massSending->total_contacts) * 100 : 0;
                    @endphp
                    {{ number_format($percentage, 1) }}% do total
                </p>
            </div>

            <!-- Delivered Count -->
            <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Entregues</h3>
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($massSending->delivered_count) }}</p>
                <p class="text-xs text-teal-600 mt-2 flex items-center">
                    @php
                        $deliveryRate = $massSending->sent_count > 0 ? ($massSending->delivered_count / $massSending->sent_count) * 100 : 0;
                    @endphp
                    {{ number_format($deliveryRate, 1) }}% taxa de entrega
                </p>
            </div>
        </div>

        <!-- Mass Sending Message -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-base font-semibold text-foreground">Mensagem da Campanha</h2>
                        <p class="text-sm text-muted-foreground">Conteúdo enviado aos contatos</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        {{ $massSending->message_type ?? 'texto' }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 border border-gray-200">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap leading-relaxed">{{ $massSending->message }}</p>
                </div>
            </div>
        </div>

        <!-- Mass Sending Details & Contacts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Mass Sending Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-semibold text-foreground">Detalhes da Campanha</h2>
                            <p class="text-sm text-muted-foreground">Informações e cronologia</p>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-border">
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="w-1 h-12 rounded-full flex-shrink-0 bg-gray-400"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Criada em</p>
                            <p class="text-sm font-medium text-foreground">{{ $massSending->created_at->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                        <svg class="w-4 h-4 text-muted-foreground flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    @if($massSending->scheduled_at)
                        <div class="flex items-center gap-4 px-6 py-4">
                            <div class="w-1 h-12 rounded-full flex-shrink-0 bg-blue-500"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Agendada para</p>
                                <p class="text-sm font-medium text-foreground">{{ $massSending->scheduled_at->format('d/m/Y \à\s H:i') }}</p>
                            </div>
                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    @endif

                    @if($massSending->started_at)
                        <div class="flex items-center gap-4 px-6 py-4">
                            <div class="w-1 h-12 rounded-full flex-shrink-0 bg-green-500"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Iniciada em</p>
                                <p class="text-sm font-medium text-foreground">{{ $massSending->started_at->format('d/m/Y \à\s H:i') }}</p>
                            </div>
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    @endif

                    @if($massSending->completed_at)
                        <div class="flex items-center gap-4 px-6 py-4">
                            <div class="w-1 h-12 rounded-full flex-shrink-0 bg-purple-500"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Concluída em</p>
                                <p class="text-sm font-medium text-foreground">{{ $massSending->completed_at->format('d/m/Y \à\s H:i') }}</p>
                            </div>
                            <svg class="w-4 h-4 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Participants List -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Card Header -->
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-semibold text-foreground">Leads da Campanha</h2>
                            <p class="text-sm text-muted-foreground">Contatos que receberão a mensagem</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-primary/10 text-primary">
                            {{ count($wuzapiParticipants) }} {{ count($wuzapiParticipants) === 1 ? 'lead' : 'leads' }}
                        </span>
                    </div>
                </div>

                <!-- Participants List Items -->
                @if(count($wuzapiParticipants) > 0)
                    <div class="divide-y divide-border max-h-[500px] overflow-y-auto">
                        @foreach($wuzapiParticipants as $index => $participant)
                            <div class="flex items-center gap-4 px-6 py-4 hover:bg-accent/30 transition-all duration-200 group">
                                <!-- Status Indicator Bar -->
                                <div class="w-1 h-14 rounded-full flex-shrink-0 bg-green-500"></div>
                                
                                <!-- Participant Info -->
                                <div class="flex-1 min-w-0">
                                    @if(!empty($participant['name']))
                                        <h3 class="text-sm font-semibold text-foreground truncate group-hover:text-primary transition-colors">
                                            {{ $participant['name'] }}
                                        </h3>
                                        <p class="text-xs text-muted-foreground mt-0.5">{{ $participant['phone'] }}</p>
                                    @else
                                        <h3 class="text-sm font-semibold text-foreground truncate group-hover:text-primary transition-colors">
                                            {{ $participant['phone'] }}
                                        </h3>
                                        <p class="text-xs text-muted-foreground mt-0.5 italic">Sem nome registrado</p>
                                    @endif
                                </div>
                                
                                <!-- WhatsApp Badge -->
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <div class="flex items-center gap-1">
                                        <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Arrow Icon -->
                                    <svg class="w-4 h-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 px-6">
                        <div class="w-16 h-16 bg-muted rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-foreground mb-2">Nenhum participante encontrado</h3>
                        <p class="text-sm text-muted-foreground">
                            Esta campanha não possui leads associados
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startBtn = document.getElementById('start-mass-sending-btn');
    const progressModal = document.getElementById('progress-modal');
    const progressMessage = document.getElementById('progress-message');
    const progressBar = document.getElementById('progress-bar');
    const progressSent = document.getElementById('progress-sent');
    const progressFailed = document.getElementById('progress-failed');
    const progressTotal = document.getElementById('progress-total');
    const closeProgress = document.getElementById('close-progress');
    const stopPolling = document.getElementById('stop-polling');
    
    let progressInterval;
    
    // Show progress modal when mass sending starts
    if (startBtn) {
        startBtn.addEventListener('click', function() {
            console.log('Botão clicado! Mostrando modal...');
            
            // Show toast notification
            const toast = document.getElementById('toast-notification');
            if (toast) {
                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 5000); // Hide after 5 seconds
            }
            
            // Show progress modal
            progressModal.classList.remove('hidden');
            startProgressPolling();
        });
    }
    
    // Handle resume buttons
    const resumeBtn = document.getElementById('resume-mass-sending-btn');
    const resumeFailedBtn = document.getElementById('resume-failed-mass-sending-btn');
    
    [resumeBtn, resumeFailedBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                console.log('Retomando campanha...');
                
                // Show toast notification
                const toast = document.getElementById('toast-notification');
                if (toast) {
                    toast.querySelector('#toast-message').textContent = 'Retomando campanha! Processando contatos restantes...';
                    toast.classList.remove('hidden');
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 5000);
                }
                
                // Show progress modal
                progressModal.classList.remove('hidden');
                startProgressPolling();
            });
        }
    });
    
    // Close progress modal
    closeProgress.addEventListener('click', function() {
        progressModal.classList.add('hidden');
        if (progressInterval) {
            clearInterval(progressInterval);
        }
    });
    
    // Stop polling manually
    if (stopPolling) {
        stopPolling.addEventListener('click', function() {
            if (progressInterval) {
                clearInterval(progressInterval);
                console.log('Polling stopped manually');
            }
        });
    }
    
    // Start progress polling
    function startProgressPolling() {
        let pollCount = 0;
        const maxPolls = 300; // Maximum 5 minutes (300 seconds)
        
        progressInterval = setInterval(function() {
            pollCount++;
            
            // Stop polling after maximum time
            if (pollCount > maxPolls) {
                clearInterval(progressInterval);
                console.log('Polling timeout reached');
                return;
            }
            
            fetch(`/mass-sendings/{{ $massSending->id }}/progress`)
                .then(response => response.json())
                .then(data => {
                    updateProgress(data);
                    
                    // Stop polling if mass sending is completed, failed, or not started
                    if (data.status === 'completed' || data.status === 'failed' || data.status === 'not_started') {
                        clearInterval(progressInterval);
                        console.log('Polling stopped - status:', data.status);
                    }
                })
                .catch(error => {
                    console.error('Error fetching progress:', error);
                    // Stop polling on error after some attempts
                    if (pollCount > 10) {
                        clearInterval(progressInterval);
                    }
                });
        }, 1000); // Poll every second
    }
    
    // Update progress UI
    function updateProgress(data) {
        progressMessage.textContent = data.current_message || 'Processando...';
        progressSent.textContent = data.sent || 0;
        progressFailed.textContent = data.failed || 0;
        progressTotal.textContent = data.total || 0;
        
        // Calculate progress percentage
        if (data.total > 0) {
            const processed = (data.sent || 0) + (data.failed || 0);
            const percentage = Math.min((processed / data.total) * 100, 100);
            progressBar.style.width = percentage + '%';
            
        }
        
        // Update modal title based on status
        const modalTitle = document.querySelector('#progress-modal h3');
        if (data.status === 'completed') {
            modalTitle.textContent = 'Envio em massa Concluída!';
            modalTitle.className = 'text-lg font-semibold text-green-600 mb-2';
            
            // Show success notification
            const successNotification = document.getElementById('success-notification');
            if (successNotification) {
                successNotification.classList.remove('hidden');
                setTimeout(() => {
                    successNotification.classList.add('hidden');
                }, 8000); // Hide after 8 seconds
            }
            
            
        } else if (data.status === 'failed') {
            modalTitle.textContent = 'Envio em massa Falhou';
            modalTitle.className = 'text-lg font-semibold text-red-600 mb-2';
            
        }
    }
    
    // Check if mass sending is already active and start polling
    @if($massSending->status === 'active')
        // Start polling automatically for active mass-sendings
        startProgressPolling();
    @endif
});
</script>
@endsection
