@extends('layouts.app')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 animate-fadeIn">
    <div class="mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar para ligações
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detalhes da Ligação</h1>
                    <p class="mt-2 text-sm text-gray-600">Informações e estado da sua ligação WhatsApp</p>
                </div>
            </div>
        </div>

        <!-- Connection Info -->
        <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Informações da Ligação</h2>
                <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Número</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $whatsapp->phone_number }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Instance ID</label>
                    <code class="inline-block bg-gray-100 px-3 py-2 rounded-lg text-sm text-gray-700 font-mono">
                        {{ $whatsapp->instance_id }}
                    </code>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold
                        @if($whatsapp->status === 'connected') bg-green-100 text-green-700 border border-green-200
                        @elseif($whatsapp->status === 'disconnected') bg-red-100 text-red-700 border border-red-200
                        @else bg-amber-100 text-amber-700 border border-amber-200 @endif">
                        <span class="w-2 h-2 rounded-full mr-2
                            @if($whatsapp->status === 'connected') bg-green-500 badge-pulse
                            @elseif($whatsapp->status === 'disconnected') bg-red-500
                            @else bg-amber-500 @endif">
                        </span>
                        {{ $whatsapp->status === 'connected' ? 'Ligado' : ($whatsapp->status === 'disconnected' ? 'Desconectado' : ucfirst($whatsapp->status)) }}
                    </span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Última Sincronização</label>
                    <div class="flex items-center text-sm text-gray-900">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $whatsapp->last_sync ? $whatsapp->last_sync->format('d/m/Y \à\s H:i') : 'Nunca sincronizado' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        @if($whatsapp->status === 'connected')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('contacts.index') }}" class="card-hover bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-2xl p-6 hover:shadow-xl transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Ver Grupos e Contactos</h3>
                            <p class="text-sm text-gray-600 mt-1">Aceda grupos e contactos via API</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('mass-sendings.create') }}" class="card-hover bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-2xl p-6 hover:shadow-xl transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Criar Campanha</h3>
                            <p class="text-sm text-gray-600 mt-1">Envie mensagens para grupos</p>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
