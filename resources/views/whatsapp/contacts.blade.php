@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <!-- Header -->
    <div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-foreground">Contactos</h1>
                <p class="text-muted-foreground mt-1">Faça a gestão seus contactos do WhatsApp</p>
            </div>
            <div class="space-x-2">
                <a href="{{ route('whatsapp.status') }}" class="px-3 py-2 rounded bg-gray-200">Status</a>
                <a href="{{ route('whatsapp.contacts') }}" class="px-3 py-2 rounded bg-green-600 text-white">Contactos</a>
                <a href="{{ route('whatsapp.index') }}" class="px-3 py-2 rounded bg-gray-200">Ligações</a>
            </div>
        </div>
    </div>

        @if(!$result['success'])
            <div class="bg-red-100 border-l-4 border-red-500 p-4">{{ $result['message'] }}</div>
        @else
            <div class="bg-white rounded shadow p-4 mb-6">
                <h2 class="font-semibold mb-2">Lista de Contactos (da conta conectada)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-600">
                                <th class="p-2">JID</th>
                                <th class="p-2">PushName</th>
                                <th class="p-2">Found</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($result['data'] as $jid => $contact)
                            <tr class="border-t">
                                <td class="p-2 font-mono text-xs">{{ $jid }}</td>
                                <td class="p-2">{{ $contact['PushName'] ?? '' }}</td>
                                <td class="p-2">{{ ($contact['Found'] ?? false) ? 'Sim' : 'Não' }}</td>
                            </tr>
                        @empty
                            <tr><td class="p-2" colspan="3">Sem contactos retornados.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded shadow p-4">
                    <h3 class="font-semibold mb-2">User Info</h3>
                    <form method="POST" action="{{ route('whatsapp.user-info') }}" class="space-y-2">
                        @csrf
                        <textarea name="phones" rows="3" class="w-full border rounded p-2" placeholder="Telefones separados por quebra de linha ou vírgula">{{ old('phones') }}</textarea>
                        <button class="px-3 py-2 bg-blue-600 text-white rounded">Procurar Info</button>
                    </form>
                    @if(session('result_info'))
                        <pre class="mt-3 text-xs bg-gray-50 p-2 rounded overflow-auto">{{ json_encode(session('result_info'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h3 class="font-semibold mb-2">Check Users (tem WhatsApp?)</h3>
                    <form method="POST" action="{{ route('whatsapp.user-check') }}" class="space-y-2">
                        @csrf
                        <textarea name="phones" rows="3" class="w-full border rounded p-2" placeholder="Telefones separados por quebra de linha ou vírgula">{{ old('phones') }}</textarea>
                        <button class="px-3 py-2 bg-blue-600 text-white rounded">Checar</button>
                    </form>
                    @if(session('result_check'))
                        <pre class="mt-3 text-xs bg-gray-50 p-2 rounded overflow-auto">{{ json_encode(session('result_check'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white rounded shadow p-4">
                    <h3 class="font-semibold mb-2">Set Presence</h3>
                    <form method="POST" action="{{ route('whatsapp.presence') }}" class="space-y-2">
                        @csrf
                        <select name="type" class="border rounded p-2 w-full">
                            <option value="available">available</option>
                            <option value="unavailable">unavailable</option>
                        </select>
                        <button class="px-3 py-2 bg-blue-600 text-white rounded">Enviar</button>
                    </form>
                    @if(session('result_presence'))
                        <pre class="mt-3 text-xs bg-gray-50 p-2 rounded overflow-auto">{{ json_encode(session('result_presence'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>

                <div class="bg-white rounded shadow p-4">
                    <h3 class="font-semibold mb-2">Avatar</h3>
                    <form method="POST" action="{{ route('whatsapp.avatar') }}" class="space-y-2">
                        @csrf
                        <input type="text" name="phone" class="border rounded p-2 w-full" placeholder="Telefone" value="{{ old('phone') }}"/>
                        <label class="inline-flex items-center space-x-2 text-sm"><input type="checkbox" name="preview" value="1" checked /> <span>Preview</span></label>
                        <button class="px-3 py-2 bg-blue-600 text-white rounded">Obter</button>
                    </form>
                    @if(session('result_avatar'))
                        <pre class="mt-3 text-xs bg-gray-50 p-2 rounded overflow-auto">{{ json_encode(session('result_avatar'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>

                <div class="bg-white rounded shadow p-4">
                    <h3 class="font-semibold mb-2">Enviar Texto</h3>
                    <form method="POST" action="{{ route('whatsapp.send-text') }}" class="space-y-2">
                        @csrf
                        <input type="text" name="phone" class="border rounded p-2 w-full" placeholder="Telefone" value="{{ old('phone') }}"/>
                        <textarea name="body" rows="3" class="w-full border rounded p-2" placeholder="Mensagem">{{ old('body') }}</textarea>
                        <input type="text" name="id" class="border rounded p-2 w-full" placeholder="Id opcional" value="{{ old('id') }}"/>
                        <button class="px-3 py-2 bg-blue-600 text-white rounded">Enviar</button>
                    </form>
                    @if(session('result_send'))
                        <pre class="mt-3 text-xs bg-gray-50 p-2 rounded overflow-auto">{{ json_encode(session('result_send'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mt-6">
                <div class="bg-white rounded shadow p-4">
                    <h3 class="font-semibold mb-2">Mark Read</h3>
                    <form method="POST" action="{{ route('whatsapp.mark-read') }}" class="space-y-2">
                        @csrf
                        <textarea name="ids" rows="3" class="w-full border rounded p-2" placeholder="IDs por linha">{{ old('ids') }}</textarea>
                        <input type="text" name="chat" class="border rounded p-2 w-full" placeholder="Chat" value="{{ old('chat') }}"/>
                        <input type="text" name="sender" class="border rounded p-2 w-full" placeholder="Sender" value="{{ old('sender') }}"/>
                        <button class="px-3 py-2 bg-blue-600 text-white rounded">Marcar</button>
                    </form>
                    @if(session('result_markread'))
                        <pre class="mt-3 text-xs bg-gray-50 p-2 rounded overflow-auto">{{ json_encode(session('result_markread'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h3 class="font-semibold mb-2">React / Delete</h3>
                    <form method="POST" action="{{ route('whatsapp.react') }}" class="space-y-2">
                        @csrf
                        <input type="text" name="phone" class="border rounded p-2 w-full" placeholder="Telefone" value="{{ old('phone') }}"/>
                        <input type="text" name="emoji" class="border rounded p-2 w-full" placeholder="Emoji (ex: ❤️)" value="{{ old('emoji') }}"/>
                        <input type="text" name="id" class="border rounded p-2 w-full" placeholder="Message Id" value="{{ old('id') }}"/>
                        <div class="flex space-x-2">
                            <button class="px-3 py-2 bg-blue-600 text-white rounded">Reagir</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('whatsapp.delete') }}" class="space-y-2 mt-2">
                        @csrf
                        <input type="text" name="id" class="border rounded p-2 w-full" placeholder="Message Id"/>
                        <button class="px-3 py-2 bg-red-600 text-white rounded">Eliminar</button>
                    </form>
                    @if(session('result_react') || session('result_delete'))
                        <pre class="mt-3 text-xs bg-gray-50 p-2 rounded overflow-auto">{{ json_encode(session('result_react') ?? session('result_delete'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
