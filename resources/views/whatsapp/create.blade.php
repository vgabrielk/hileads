@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Nova Conexão WhatsApp</h1>

                    <form method="POST" action="{{ route('whatsapp.store') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Número do WhatsApp
                            </label>
                            <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                                placeholder="Ex: 5511999999999" required>
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Digite o número no formato internacional (ex: 5511999999999)
                            </p>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('whatsapp.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Criar Conexão
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


