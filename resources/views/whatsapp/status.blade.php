@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <div class="max-w-6xl mx-auto">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Status da Ligação</h1>
            <div class="space-x-2">
                <a href="{{ route('whatsapp.status') }}" class="px-3 py-2 rounded bg-green-600 text-white">Status</a>
                <a href="{{ route('whatsapp.contacts') }}" class="px-3 py-2 rounded bg-gray-200">Contactos</a>
                <a href="{{ route('whatsapp.index') }}" class="px-3 py-2 rounded bg-gray-200">Ligações</a>
            </div>
        </div>

        @if(!$result['success'])
            <div class="bg-red-100 border-l-4 border-red-500 p-4">{{ $result['message'] }}</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded shadow p-4">
                    <div class="text-sm text-gray-500">Connected</div>
                    <div class="text-2xl font-semibold">{{ ($result['data']['Connected'] ?? false) ? 'Sim' : 'Não' }}</div>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <div class="text-sm text-gray-500">Logged In</div>
                    <div class="text-2xl font-semibold">{{ ($result['data']['LoggedIn'] ?? false) ? 'Sim' : 'Não' }}</div>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <div class="text-sm text-gray-500">Token (prefixo)</div>
                    <div class="text-xs font-mono">{{ substr(auth()->user()->api_token,0,16) }}...</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
