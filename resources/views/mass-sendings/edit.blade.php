@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('mass-sendings.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar para campanhas
        </a>
        <h1 class="text-3xl font-bold text-foreground">Editar Campanha</h1>
        <p class="text-muted-foreground mt-1">Modifique os detalhes da sua campanha</p>
    </div>

    <form method="POST" action="{{ route('mass-sendings.update', $massSending) }}" id="campaign-form" class="space-y-6">
            @csrf
            @method('PUT')
            
            @if(isset($group) && $group && is_object($group))
                <input type="hidden" name="group_id" value="{{ $group->id }}">
            @endif
            
            @if(isset($group) && $group && is_object($group))
                <!-- Group Info -->
                <div class="bg-primary/10 border border-primary/20 rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-primary">Enviando para o grupo: {{ $group->name }}</h3>
                            <p class="text-primary/80">{{ $group->contacts_count ?? 0 }} contatos serão incluídos neste envio</p>
                            @if(isset($group->description) && $group->description)
                                <p class="text-sm text-primary/70 mt-1">{{ $group->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Campaign Basic Info -->
            <div class="bg-card rounded-lg border border-border overflow-hidden mb-6">
                <div class="p-6 border-b border-border">
                    <h2 class="text-lg font-bold text-foreground">Informações Básicas</h2>
                    <p class="text-sm text-muted-foreground mt-1">Defina o nome e a mensagem da sua campanha</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Campaign Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-foreground mb-2">
                            Nome da Campanha
                            <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $massSending->name) }}"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                               placeholder="Ex: Campanha de Lançamento de Produto"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Campaign Message & Media -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mensagem da Campanha
                            <span class="text-red-500">*</span>
                        </label>
                        
                        <!-- Message Type Toggle -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex bg-secondary rounded-lg p-1">
                                <button type="button" id="textMode" class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 bg-primary text-primary-foreground shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Texto
                                </button>
                                <button type="button" id="mediaMode" class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-muted-foreground hover:text-foreground hover:bg-accent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Mídia
                                </button>
                            </div>
                        </div>

                        <!-- Text Message Section -->
                        <div id="textSection">
                            <textarea id="message" 
                                      name="message" 
                                      rows="6"
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                                      placeholder="Digite a mensagem que será enviada para os contatos selecionados...">{{ old('message', $massSending->message) }}</textarea>
                            <p class="mt-2 text-xs text-gray-500">A mensagem será enviada exatamente como digitada acima</p>
            </div>

                        <!-- Media Section -->
                        <div id="mediaSection" class="hidden">
                            <!-- Media Upload Buttons -->
                            <div class="flex flex-wrap gap-3 mb-4">
                                <button type="button" id="uploadImage" class="flex items-center px-4 py-3 bg-green-100 hover:bg-green-200 text-green-700 rounded-xl transition-all hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                    Imagem
                                </button>
                                
                                <button type="button" id="uploadVideo" class="flex items-center px-4 py-3 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-xl transition-all hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                    </svg>
                                    Vídeo
                                </button>
                                
                                <button type="button" id="uploadAudio" class="flex items-center px-4 py-3 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-xl transition-all hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 12a7.971 7.971 0 00-1.343-4.243 1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Áudio
                                </button>
                                
                                <button type="button" id="uploadDocument" class="flex items-center px-4 py-3 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-xl transition-all hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                    </svg>
                                    Documento
                                </button>
                </div>
                
                            <!-- Drag & Drop Area -->
                            <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-all cursor-pointer">
                    <div class="space-y-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Arraste arquivos aqui</p>
                                        <p class="text-sm text-gray-500">ou clique para selecionar</p>
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        Suporte: JPG, PNG, MP4, MP3, PDF, DOC, etc. (WebP e GIF não permitidos)
                                    </p>
                                </div>
                            </div>

                            <!-- Hidden File Input -->
                            <input type="file" id="fileInput" class="hidden" accept="image/jpeg,image/jpg,image/png,video/*,audio/*,.pdf,.doc,.docx,.txt">

                            <!-- Media Caption -->
                            <div class="mt-4">
                                <label for="mediaCaption" class="block text-sm font-medium text-gray-700 mb-2">
                                    Legenda da Mídia (opcional)
                                </label>
                                <textarea id="mediaCaption" 
                                          name="media_caption" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                                          placeholder="Digite uma legenda para acompanhar a mídia...">{{ old('media_caption', $massSending->media_caption ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div id="previewArea" class="mt-4 hidden">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">📋 Preview da Mídia</h4>
                            <div id="previewContent" class="space-y-2">
                                @if($massSending->media_data && isset($massSending->media_data['base64']))
                                    <!-- Current Image Card -->
                                    <div class="bg-white border border-gray-200 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="text-sm font-semibold text-gray-700 flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Imagem Atual
                                            </h5>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="relative">
                                                @php
                                                    $base64Data = $massSending->media_data['base64'];
                                                    // Se o base64 já tem o prefixo data:, usa direto
                                                    if (str_starts_with($base64Data, 'data:')) {
                                                        $imageSrc = $base64Data;
                                                    } else {
                                                        // Se não tem prefixo, adiciona
                                                        $imageSrc = 'data:' . $massSending->media_data['type'] . ';base64,' . $base64Data;
                                                    }
                                                @endphp
                                                <img src="{{ $imageSrc }}" 
                                                     alt="Imagem atual" 
                                                     class="w-full h-48 object-cover rounded-lg border border-gray-200"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                <div style="display:none;" class="w-full h-48 bg-red-100 border border-red-300 rounded-lg flex items-center justify-center">
                                                    <div class="text-center text-red-600">
                                                        <p class="text-sm font-medium">Erro ao carregar imagem</p>
                                                        <p class="text-xs mt-1">Base64 pode estar corrompido</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <p class="font-medium">{{ $massSending->media_data['name'] ?? 'Arquivo de mídia' }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Carregue uma nova imagem acima para substituir esta
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- No current media -->
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm">Nenhuma mídia selecionada</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @error('message')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Schedule -->
                    <div>
                        <label for="scheduled_at" class="block text-sm font-semibold text-gray-700 mb-2">
                            Agendar Envio
                            <span class="text-gray-500 font-normal">(opcional)</span>
                        </label>
                        <input type="datetime-local" 
                               id="scheduled_at" 
                               name="scheduled_at" 
                               value="{{ old('scheduled_at', $massSending->scheduled_at ? \Carbon\Carbon::parse($massSending->scheduled_at)->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <p class="mt-2 text-xs text-gray-500">Deixe em branco para enviar imediatamente após iniciar</p>
                        @error('scheduled_at')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                </div>
                </div>
            </div>

            <!-- Selected Groups Info (Read-only) -->
            @if($massSending->groups && $massSending->groups->count() > 0)
                <div class="bg-card rounded-lg border border-border overflow-hidden mb-6">
                    <div class="p-6 border-b border-border">
                        <h2 class="text-lg font-bold text-foreground">Grupos Selecionados</h2>
                        <p class="text-sm text-muted-foreground mt-1">Grupos que receberão esta campanha</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($massSending->groups as $group)
                                <div class="bg-secondary rounded-lg p-4 flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-foreground truncate">{{ $group->name }}</h3>
                                        <p class="text-sm text-muted-foreground">{{ $group->contacts_count ?? 0 }} contatos</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success/10 text-success">
                                            Selecionado
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 p-3 bg-primary/10 rounded-lg">
                            <p class="text-sm text-primary font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Para alterar os grupos, você precisa criar uma nova campanha.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info Card -->
            @error('wuzapi_participants')
                <div class="bg-red-50 border-l-4 border-red-500 rounded-xl p-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-red-800">{{ $message }}</p>
                    </div>
                </div>
            @enderror

            <!-- Manual Contacts -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Adicionar Contatos Manualmente</h2>
                <div id="manualContactsContainer">
                    <!-- Contatos serão adicionados aqui dinamicamente -->
                </div>
                
                <button type="button" onclick="addManualContact()" class="btn-ripple inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Adicionar Contato
                </button>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 bg-card rounded-lg border border-border p-6">
                <a href="{{ route('mass-sendings.index') }}" class="px-4 py-2 text-muted-foreground hover:text-foreground font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Salvar Alterações
                </button>
            </div>
    </form>
</div>

<script>
// Groups functionality removed - not needed in edit mode


// Media functionality
let selectedFiles = [];
let currentMessageType = 'text';

// Manual contacts functionality
let manualContactIndex = 1;

function addManualContact() {
    const container = document.getElementById('manualContactsContainer');
    const newContact = document.createElement('div');
    newContact.className = 'manual-contact-item flex items-center space-x-4 p-4 border border-gray-200 rounded-xl mb-3';
    newContact.innerHTML = `
        <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="manual_contacts[${manualContactIndex}][name]" placeholder="Nome do contato"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                    <div class="flex">
                        <div class="flex items-center px-3 py-2 bg-gray-50 border border-gray-300 border-r-0 rounded-l-lg">
                            <img src="https://flagcdn.com/w20/br.png" alt="BR" class="w-4 h-3 mr-2">
                            <span class="text-sm text-gray-600">+55</span>
                        </div>
                        <input type="text" name="manual_contacts[${manualContactIndex}][phone]" placeholder="11999999999" required
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>
        <button type="button" onclick="removeManualContact(this)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(newContact);
    manualContactIndex++;
}

function removeManualContact(button) {
    button.closest('.manual-contact-item').remove();
}

// Initialize media functionality
function initializeMediaFunctionality() {
    console.log('Initializing media functionality');
    // Message type toggle
    document.getElementById('textMode').addEventListener('click', () => switchToTextMode());
    document.getElementById('mediaMode').addEventListener('click', () => switchToMediaMode());
    
    // File upload buttons
    document.getElementById('uploadImage').addEventListener('click', () => openFileDialog('image'));
    document.getElementById('uploadVideo').addEventListener('click', () => openFileDialog('video'));
    document.getElementById('uploadAudio').addEventListener('click', () => openFileDialog('audio'));
    document.getElementById('uploadDocument').addEventListener('click', () => openFileDialog('document'));
    
    // Drag and drop
    const dropZone = document.getElementById('dropZone');
    dropZone.addEventListener('click', () => document.getElementById('fileInput').click());
    dropZone.addEventListener('dragover', handleDragOver);
    dropZone.addEventListener('drop', handleDrop);
    
    // File input
    document.getElementById('fileInput').addEventListener('change', handleFileSelect);
}

function switchToTextMode() {
    currentMessageType = 'text';
    document.getElementById('textSection').classList.remove('hidden');
    document.getElementById('mediaSection').classList.add('hidden');
    document.getElementById('previewArea').classList.add('hidden'); // Esconde preview no modo texto
    
    const textModeBtn = document.getElementById('textMode');
    const mediaModeBtn = document.getElementById('mediaMode');
    
    if (textModeBtn) {
        textModeBtn.classList.add('bg-primary', 'text-primary-foreground', 'shadow-sm');
        textModeBtn.classList.remove('text-muted-foreground', 'hover:text-foreground', 'hover:bg-accent');
    }
    if (mediaModeBtn) {
        mediaModeBtn.classList.add('text-muted-foreground', 'hover:text-foreground', 'hover:bg-accent');
        mediaModeBtn.classList.remove('bg-primary', 'text-primary-foreground', 'shadow-sm');
    }
}

function switchToMediaMode() {
    currentMessageType = 'media';
    console.log('Switched to media mode');
    document.getElementById('textSection').classList.add('hidden');
    document.getElementById('mediaSection').classList.remove('hidden');
    
    const textModeBtn = document.getElementById('textMode');
    const mediaModeBtn = document.getElementById('mediaMode');
    
    if (textModeBtn) {
        textModeBtn.classList.add('text-muted-foreground', 'hover:text-foreground', 'hover:bg-accent');
        textModeBtn.classList.remove('bg-primary', 'text-primary-foreground', 'shadow-sm');
    }
    if (mediaModeBtn) {
        mediaModeBtn.classList.add('bg-primary', 'text-primary-foreground', 'shadow-sm');
        mediaModeBtn.classList.remove('text-muted-foreground', 'hover:text-foreground', 'hover:bg-accent');
    }
    
    // Mostra preview se há mídia existente ou arquivo selecionado
    @if($massSending->media_data && isset($massSending->media_data['base64']))
        document.getElementById('previewArea').classList.remove('hidden');
    @endif
}

function openFileDialog(type) {
    const input = document.getElementById('fileInput');
    switch(type) {
        case 'image':
            input.accept = 'image/*';
            break;
        case 'video':
            input.accept = 'video/*';
            break;
        case 'audio':
            input.accept = 'audio/*';
            break;
        case 'document':
            input.accept = '.pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx';
            break;
    }
    input.click();
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('border-blue-400', 'bg-blue-50');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('border-blue-400', 'bg-blue-50');
    
    const files = Array.from(e.dataTransfer.files);
    processFiles(files);
}

function handleFileSelect(e) {
    const files = Array.from(e.target.files);
    processFiles(files);
}

function processFiles(files) {
    files.forEach(file => {
        if (validateFile(file)) {
            convertToBase64(file);
        }
    });
}

function validateFile(file) {
    const maxSize = 50 * 1024 * 1024; // 50MB
    if (file.size > maxSize) {
        showNotification('Arquivo muito grande. Máximo 50MB.', 'error');
        return false;
    }
    
    // Verificar se é formato WebP ou GIF (não permitidos)
    if (file.type === 'image/webp') {
        showNotification('Formato WebP não é permitido. Use apenas JPG ou PNG.', 'error');
        return false;
    }
    
    if (file.type === 'image/gif') {
        showNotification('Formato GIF não é permitido. Use apenas JPG ou PNG.', 'error');
        return false;
    }
    
    return true;
}

function convertToBase64(file) {
    console.log('Converting file to base64:', file.name);
    const reader = new FileReader();
    reader.onload = function(e) {
        const base64 = e.target.result;
        const fileData = {
            name: file.name,
            size: file.size,
            type: file.type,
            base64: base64,
            fileType: getFileType(file.type)
        };
        
        console.log('File converted:', fileData);
        selectedFiles = [fileData]; // Only one file for mass-sendings
        updatePreview();
    };
    reader.readAsDataURL(file);
}

function getFileType(mimeType) {
    if (mimeType.startsWith('image/')) return 'image';
    if (mimeType.startsWith('video/')) return 'video';
    if (mimeType.startsWith('audio/')) return 'audio';
    return 'document';
}

function updatePreview() {
    const previewArea = document.getElementById('previewArea');
    const previewContent = document.getElementById('previewContent');
    
    // Só mostra preview se estiver no modo mídia
    if (currentMessageType !== 'media') {
        previewArea.classList.add('hidden');
        return;
    }
    
    if (selectedFiles.length === 0) {
        // Se não há arquivos selecionados, mostra a imagem atual se existir
        @if($massSending->media_data && isset($massSending->media_data['base64']))
            previewArea.classList.remove('hidden');
            @php
                $base64Data = $massSending->media_data['base64'];
                if (str_starts_with($base64Data, 'data:')) {
                    $imageSrc = $base64Data;
                } else {
                    $imageSrc = 'data:' . $massSending->media_data['type'] . ';base64,' . $base64Data;
                }
            @endphp
            previewContent.innerHTML = `
                    <div class="bg-white border border-gray-200 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Imagem Atual
                            </h5>
                        </div>
                        <div class="space-y-3">
                            <div class="relative">
                                <img src="{{ $imageSrc }}" 
                                     alt="Imagem atual" 
                                     class="w-full h-48 object-cover rounded-lg border border-gray-200">
                            </div>
                            <div class="text-sm text-gray-600">
                                <p class="font-medium">{{ $massSending->media_data['name'] ?? 'Arquivo de mídia' }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Carregue uma nova imagem acima para substituir esta
                                </p>
                            </div>
                        </div>
                    </div>
                `;
        @else
            previewArea.classList.add('hidden');
        @endif
        return;
    }

    previewArea.classList.remove('hidden');
    
    // Substitui a imagem no mesmo card ao invés de criar um novo
    const file = selectedFiles[0]; // Apenas um arquivo para mass-sendings
    const fileIcon = getFileIcon(file.fileType);
    const fileSize = formatFileSize(file.size);
    
    previewContent.innerHTML = `
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <h5 class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Nova Imagem
                </h5>
            </div>
            <div class="space-y-3">
                <div class="relative">
                    <img src="${file.base64}" 
                         alt="Nova imagem" 
                         class="w-full h-48 object-cover rounded-lg border border-gray-200"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div style="display:none;" class="w-full h-48 bg-red-100 border border-red-300 rounded-lg flex items-center justify-center">
                        <div class="text-center text-red-600">
                            <p class="text-sm font-medium">Erro ao carregar imagem</p>
                            <p class="text-xs mt-1">Formato não suportado</p>
                        </div>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <p class="font-medium">${file.name}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Carregue uma nova imagem acima para substituir esta
                    </p>
                </div>
            </div>
        </div>
    `;
}


function getFileIcon(type) {
    const icons = {
        'image': '🖼️',
        'video': '🎥',
        'audio': '🎵',
        'document': '📄'
    };
    return icons[type] || '📄';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}


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

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Check if there's existing media and set the correct mode
    @if($massSending->media_data && isset($massSending->media_data['base64']))
        currentMessageType = 'media';
        switchToMediaMode();
    @else
        currentMessageType = 'text';
        switchToTextMode();
    @endif
    
    // Initialize media functionality
    initializeMediaFunctionality();
    
    // Override form submission to include media data
    const campaignForm = document.getElementById('campaign-form');
    if (campaignForm) {
        campaignForm.addEventListener('submit', function(e) {
            console.log('Form submission - currentMessageType:', currentMessageType);
            console.log('Form submission - selectedFiles:', selectedFiles.length);
            // alert('Form submitting - Type: ' + currentMessageType + ', Files: ' + selectedFiles.length);
            
            // Add media data to form before submission
            if (currentMessageType === 'media' && selectedFiles.length > 0) {
                const file = selectedFiles[0];
                console.log('Adding media data:', file);
                
                // Remove any existing media inputs
                const existingMediaType = document.querySelector('input[name="media_type"]');
                const existingMediaData = document.querySelector('input[name="media_data"]');
                if (existingMediaType) existingMediaType.remove();
                if (existingMediaData) existingMediaData.remove();
                
            // Create hidden inputs for media data
            const mediaTypeInput = document.createElement('input');
            mediaTypeInput.type = 'hidden';
            mediaTypeInput.name = 'media_type';
                mediaTypeInput.value = file.fileType;
                campaignForm.appendChild(mediaTypeInput);
            
            const mediaDataInput = document.createElement('input');
            mediaDataInput.type = 'hidden';
            mediaDataInput.name = 'media_data';
            mediaDataInput.value = JSON.stringify({
                    name: file.name,
                    type: file.type,
                    base64: file.base64,
                    size: file.size
                });
                campaignForm.appendChild(mediaDataInput);
                
                // Clear the text message if using media
                const messageInput = document.getElementById('message');
                if (messageInput) {
                    messageInput.value = '';
                }
                
                console.log('Media inputs added to form');
            } else {
                console.log('No media selected, using text mode');
            }
        });
    }
});
</script>
@endsection