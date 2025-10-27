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
        <h1 class="text-3xl font-bold text-foreground">Nova Campanha</h1>
        <p class="text-muted-foreground mt-1">Configure e crie uma nova campanha de marketing</p>
    </div>

    <form method="POST" action="{{ route('mass-sendings.store') }}" id="campaign-form" class="space-y-6">
            @csrf
            
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
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                               placeholder="Ex: Campanha de Lançamento de Produto"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-destructive flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Campaign Message & Media -->
                    <div>
                        <label class="block text-sm font-semibold text-foreground mb-2">
                            Mensagem da Campanha
                            <span class="text-destructive">*</span>
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
                                      class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none bg-background text-foreground"
                                      placeholder="Digite a mensagem que será enviada para os contatos selecionados...">{{ old('message') }}</textarea>
                            <p class="mt-2 text-xs text-muted-foreground">A mensagem será enviada exatamente como digitada acima</p>
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
                                        Suporte: JPG, PNG, GIF, MP4, MP3, PDF, DOC, etc.
                                    </p>
                                </div>
                            </div>

                            <!-- Hidden File Input -->
                            <input type="file" id="fileInput" class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt">

                            <!-- Media Caption -->
                            <div class="mt-4">
                                <label for="mediaCaption" class="block text-sm font-medium text-gray-700 mb-2">
                                    Legenda da Mídia (opcional)
                                </label>
                                <textarea id="mediaCaption" 
                                          name="media_caption" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                                          placeholder="Digite uma legenda para acompanhar a mídia...">{{ old('media_caption') }}</textarea>
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div id="previewArea" class="mt-4 hidden">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">📋 Preview da Mídia</h4>
                            <div id="previewContent" class="space-y-2">
                                <!-- Preview content will be inserted here -->
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
                        <label for="scheduled_at" class="block text-sm font-semibold text-foreground mb-2">
                            Agendar Envio
                            <span class="text-muted-foreground font-normal">(opcional)</span>
                        </label>
                        <input type="datetime-local" 
                               id="scheduled_at" 
                               name="scheduled_at" 
                               value="{{ old('scheduled_at') }}"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground">
                        <p class="mt-2 text-xs text-muted-foreground">Deixe em branco para enviar imediatamente após iniciar</p>
                        @error('scheduled_at')
                            <p class="mt-2 text-sm text-destructive flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- WhatsApp Groups Selection with Chips -->
            @if(isset($validGroups) && count($validGroups) > 0)
                <div class="bg-card rounded-lg border border-border overflow-hidden mb-6">
                    <div class="p-6 border-b border-border">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-lg font-bold text-foreground mb-1">📱 Selecionar Grupos do WhatsApp</h2>
                                <p class="text-sm text-muted-foreground">
                                    Escolha de quais grupos do seu WhatsApp você quer extrair os leads/contatos para a campanha. 
                                    <span class="font-semibold text-primary">Todos os participantes dos grupos selecionados serão incluídos!</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($apiError)
                            <div class="bg-destructive/10 border-l-4 border-destructive rounded-lg p-6 mb-6">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-destructive mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm text-destructive font-semibold">
                                            @if($connectionIssue)
                                                Problema de Conexão WhatsApp
                                            @else
                                                Erro ao carregar grupos do WhatsApp
                                            @endif
                                        </p>
                                        <p class="text-sm text-destructive/80 mt-1">
                                            {{ $apiErrorMessage ?? 'Não foi possível conectar com a API do Wuzapi. Verifique sua conexão com o WhatsApp.' }}
                                        </p>
                                        
                                        @if($needsConnection)
                                            <p class="text-xs text-destructive/70 mt-2">
                                                <strong>Problema:</strong> WhatsApp não está conectado. Você precisa conectar-se primeiro.
                                            </p>
                                        @elseif($needsLogin)
                                            <p class="text-xs text-destructive/70 mt-2">
                                                <strong>Problema:</strong> WhatsApp não está logado. Faça login para acessar os grupos.
                                            </p>
                                        @else
                                            <p class="text-xs text-destructive/70 mt-2">
                                                <strong>Dica:</strong> Certifique-se de que você está conectado ao WhatsApp e que sua sessão está ativa.
                                            </p>
                                        @endif
                                        
                                        <p class="text-xs text-warning-foreground mt-2 bg-warning/10 p-2 rounded">
                                            <strong>Modo Demonstração:</strong> Mostrando grupos de exemplo para você testar a funcionalidade.
                                        </p>
                                        
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <button type="button" 
                                                    onclick="location.reload()"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-destructive-foreground bg-destructive hover:bg-destructive/90 rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Tentar Novamente
                                            </button>
                                            
                                            <button type="button" 
                                                    onclick="reconnectWhatsApp()"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Reconectar WhatsApp
                                            </button>
                                            
                                            <button type="button" 
                                                    onclick="regenerateToken()"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-secondary-foreground bg-secondary hover:bg-secondary/90 rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                                                </svg>
                                                Regenerar Token
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(count($validGroups) == 0 && !$apiError)
                            <div class="bg-primary/10 border-l-4 border-primary rounded-lg p-6 mb-6">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-primary mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-primary font-semibold">
                                            Nenhum grupo do WhatsApp encontrado
                                        </p>
                                        <p class="text-sm text-primary/80 mt-1">
                                            Não foram encontrados grupos com participantes válidos no seu WhatsApp.
                                        </p>
                                        <p class="text-xs text-primary/70 mt-2">
                                            <strong>Possíveis causas:</strong>
                                        </p>
                                        <ul class="text-xs text-primary/70 mt-1 ml-4 list-disc">
                                            <li>Você não está em nenhum grupo do WhatsApp</li>
                                            <li>Os grupos não têm participantes com números válidos</li>
                                            <li>Problema temporário com a API</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Groups Selection with Chips -->
                        <div class="space-y-4">
                            <!-- Selected Groups Chips -->
                            <div id="selected-groups-chips" class="hidden">
                                <label class="block text-sm font-medium text-foreground mb-2">Grupos Selecionados</label>
                                <div id="chips-container" class="flex flex-wrap gap-2 p-3 border border-input rounded-lg bg-muted/50 min-h-[50px]">
                                    <!-- Chips will be added here dynamically -->
                                </div>
                            </div>
                            
                            <!-- Groups Search and Selection -->
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Selecionar Grupos</label>
                                <div class="relative">
                                    <input type="text" 
                                           id="groups-search" 
                                           placeholder="Buscar grupos..." 
                                           class="w-full px-4 py-3 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground">
                                    <svg class="absolute right-3 top-3.5 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Groups List -->
                            <div id="groups-list" class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($validGroups as $index => $groupData)
                                    <div class="group-item bg-card border border-border rounded-lg p-4 hover:border-primary/50 hover:shadow-sm transition-all cursor-pointer"
                                         data-group-jid="{{ $groupData['jid'] }}"
                                         data-group-index="{{ $index }}"
                                         data-group-name="{{ strtolower($groupData['name']) }}">
                                        <label class="cursor-pointer block">
                                            <div class="flex items-center gap-4">
                                                <!-- Checkbox -->
                                                <div class="flex-shrink-0">
                                                    <input type="checkbox" 
                                                           class="group-checkbox w-4 h-4 rounded border-input text-primary focus:ring-primary"
                                                           data-group-index="{{ $index }}"
                                                           data-group-jid="{{ $groupData['jid'] }}"
                                                           onchange="toggleWuzapiGroup('{{ $index }}')">
                                                    <!-- Hidden inputs for valid participant JIDs -->
                                                    @foreach($groupData['valid_participants'] as $jid)
                                                        <input type="hidden" 
                                                               class="wuzapi-participant wuzapi-group-{{ $index }}" 
                                                               name="wuzapi_participants[]" 
                                                               value="{{ $jid }}"
                                                               disabled>
                                                    @endforeach
                                                </div>
                                                
                                                <!-- Group Icon -->
                                                @if($groupData['photo'])
                                                    <img src="{{ $groupData['photo'] }}" alt="{{ $groupData['name'] }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                                @else
                                                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                
                                                <!-- Group Info -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <h3 class="font-semibold text-foreground text-sm truncate">{{ $groupData['name'] }}</h3>
                                                        @if(isset($groupData['is_example']) && $groupData['is_example'])
                                                            <span class="inline-flex items-center px-2 py-0.5 bg-warning/10 text-warning rounded-full text-xs font-medium">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Exemplo
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center text-xs text-muted-foreground">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                        <span class="font-medium text-primary">{{ $groupData['valid_count'] }} leads</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Summary -->
                        <div id="groups-summary" class="mt-6 p-4 bg-primary/10 rounded-lg border border-primary/20 hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-foreground">
                                            <span id="selected-groups-count">0</span> grupos selecionados
                                        </p>
                                        <p class="text-xs text-muted-foreground">
                                            Total de <span id="selected-contacts-from-groups" class="font-semibold text-primary">0</span> leads serão extraídos
                                        </p>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="clearAllGroups()"
                                        class="text-sm text-destructive hover:text-destructive/80 font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Limpar seleção
                                </button>
                            </div>
                        </div>

                        <!-- Extracted Leads Preview -->
                        <div id="extracted-leads-preview" class="mt-6 p-4 bg-success/10 rounded-lg border border-success/20 hidden">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-success rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-success-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-foreground">Leads Extraídos dos Grupos</h3>
                                        <p class="text-sm text-muted-foreground">Lista dos contatos que serão incluídos na campanha</p>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="toggleLeadsPreview()"
                                        class="text-sm text-success hover:text-success/80 font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span id="toggle-leads-text">Ver leads</span>
                                </button>
                            </div>
                            
                            <div id="leads-list" class="hidden">
                                <div class="bg-card rounded-lg border border-border max-h-60 overflow-y-auto">
                                    <div id="leads-content" class="p-4">
                                        <!-- Leads serão inseridos aqui via JavaScript -->
                                    </div>
                                </div>
                            </div>
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
            <div class="flex items-center justify-end space-x-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <a href="{{ route('mass-sendings.index') }}" class="btn-ripple px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all">
                    Cancelar
                </a>
                <button type="submit" class="btn-ripple px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-sm transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Criar Campanha
                </button>
            </div>
    </form>
</div>

<script>
// Store Wuzapi groups data
const wuzapiGroupsData = {
    @foreach($validGroups as $index => $groupData)
        '{{ $index }}': {
            jid: '{{ $groupData['jid'] }}',
            name: '{{ addslashes($groupData['name']) }}',
            participantsCount: {{ $groupData['valid_count'] }},
            validParticipants: @json($groupData['valid_participants'])
        },
    @endforeach
};

// Toggle Wuzapi group selection
function toggleWuzapiGroup(groupIndex) {
    const checkbox = document.querySelector(`.group-checkbox[data-group-index="${groupIndex}"]`);
    if (!checkbox) return;
    
    const isChecked = checkbox.checked;
    const groupItem = document.querySelector(`.group-item[data-group-index="${groupIndex}"]`);
    if (!groupItem) return;
    
    // Toggle visual state
    if (isChecked) {
        groupItem.classList.add('border-primary', 'bg-primary/5');
        addGroupChip(groupIndex);
    } else {
        groupItem.classList.remove('border-primary', 'bg-primary/5');
        removeGroupChip(groupIndex);
    }
    
    // Enable/disable participant hidden inputs
    const participantInputs = document.querySelectorAll(`.wuzapi-group-${groupIndex}`);
    participantInputs.forEach(input => {
        input.disabled = !isChecked;
    });
    
    updateGroupsSummary();
}

// Add group chip
function addGroupChip(groupIndex) {
    const groupData = wuzapiGroupsData[groupIndex];
    if (!groupData) return;
    
    const chipsContainer = document.getElementById('chips-container');
    const selectedChips = document.getElementById('selected-groups-chips');
    
    // Show chips container if hidden
    selectedChips.classList.remove('hidden');
    
    // Create chip element
    const chip = document.createElement('div');
    chip.className = 'inline-flex items-center gap-2 px-3 py-1.5 bg-primary text-primary-foreground rounded-lg text-sm font-medium';
    chip.id = `chip-${groupIndex}`;
    chip.innerHTML = `
        <span>${groupData.name}</span>
        <button type="button" onclick="removeGroupChip('${groupIndex}')" class="hover:bg-primary-foreground/20 rounded-full p-0.5">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    chipsContainer.appendChild(chip);
}

// Remove group chip
function removeGroupChip(groupIndex) {
    const chip = document.getElementById(`chip-${groupIndex}`);
    if (chip) {
        chip.remove();
    }
    
    // Uncheck the checkbox
    const checkbox = document.querySelector(`.group-checkbox[data-group-index="${groupIndex}"]`);
    if (checkbox) {
        checkbox.checked = false;
        toggleWuzapiGroup(groupIndex);
    }
    
    // Hide chips container if empty
    const chipsContainer = document.getElementById('chips-container');
    const selectedChips = document.getElementById('selected-groups-chips');
    if (chipsContainer.children.length === 0) {
        selectedChips.classList.add('hidden');
    }
}

// Update groups summary
function updateGroupsSummary() {
    const selectedGroups = document.querySelectorAll('.group-checkbox:checked');
    const summary = document.getElementById('groups-summary');
    const leadsPreview = document.getElementById('extracted-leads-preview');
    const countElement = document.getElementById('selected-groups-count');
    const contactsElement = document.getElementById('selected-contacts-from-groups');
    
    let totalParticipants = 0;
    let selectedGroupsData = [];
    
    selectedGroups.forEach(checkbox => {
        const groupIndex = checkbox.dataset.groupIndex;
        const groupData = wuzapiGroupsData[groupIndex];
        if (groupData) {
            totalParticipants += groupData.participantsCount;
            selectedGroupsData.push({
                name: groupData.name,
                participants: groupData.validParticipants,
                count: groupData.participantsCount
            });
        }
    });
    
    if (selectedGroups.length > 0) {
        summary.classList.remove('hidden');
        leadsPreview.classList.remove('hidden');
        countElement.textContent = selectedGroups.length;
        contactsElement.textContent = totalParticipants;
        
        // Update leads preview
        updateLeadsPreview(selectedGroupsData);
    } else {
        summary.classList.add('hidden');
        leadsPreview.classList.add('hidden');
    }
}

// Update leads preview
function updateLeadsPreview(selectedGroupsData) {
    const leadsContent = document.getElementById('leads-content');
    let html = '';
    
    selectedGroupsData.forEach(group => {
        html += `
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    ${group.name} (${group.count} leads)
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
        `;
        
        group.participants.forEach(participant => {
            const phone = participant.replace('@s.whatsapp.net', '');
            html += `
                <div class="flex items-center p-2 bg-white rounded border text-sm">
                    <svg class="w-3 h-3 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                    <span class="text-gray-700">${phone}</span>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    leadsContent.innerHTML = html;
}

// Toggle leads preview
function toggleLeadsPreview() {
    const leadsList = document.getElementById('leads-list');
    const toggleText = document.getElementById('toggle-leads-text');
    
    if (leadsList.classList.contains('hidden')) {
        leadsList.classList.remove('hidden');
        toggleText.textContent = 'Ocultar leads';
    } else {
        leadsList.classList.add('hidden');
        toggleText.textContent = 'Ver leads';
    }
}

// Clear all groups
function clearAllGroups() {
    document.querySelectorAll('.group-checkbox').forEach(checkbox => {
        if (checkbox.checked) {
            checkbox.checked = false;
            const groupIndex = checkbox.dataset.groupIndex;
            toggleWuzapiGroup(groupIndex);
        }
    });
    
    // Hide chips container
    const selectedChips = document.getElementById('selected-groups-chips');
    selectedChips.classList.add('hidden');
}

// Search groups
function initializeGroupSearch() {
    const searchInput = document.getElementById('groups-search');
    const groupsList = document.getElementById('groups-list');
    
    if (searchInput && groupsList) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const groupItems = groupsList.querySelectorAll('.group-item');
            
            groupItems.forEach(item => {
                const groupName = item.dataset.groupName || '';
                if (groupName.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
}


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
    
    // Check if elements exist
    const textModeBtn = document.getElementById('textMode');
    const mediaModeBtn = document.getElementById('mediaMode');
    
    if (!textModeBtn || !mediaModeBtn) {
        console.error('Mode buttons not found!');
        return;
    }
    
    // Message type toggle
    textModeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        switchToTextMode();
    });
    
    mediaModeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        switchToMediaMode();
    });
    
    // File upload buttons
    const uploadImage = document.getElementById('uploadImage');
    const uploadVideo = document.getElementById('uploadVideo');
    const uploadAudio = document.getElementById('uploadAudio');
    const uploadDocument = document.getElementById('uploadDocument');
    
    if (uploadImage) uploadImage.addEventListener('click', () => openFileDialog('image'));
    if (uploadVideo) uploadVideo.addEventListener('click', () => openFileDialog('video'));
    if (uploadAudio) uploadAudio.addEventListener('click', () => openFileDialog('audio'));
    if (uploadDocument) uploadDocument.addEventListener('click', () => openFileDialog('document'));
    
    // Drag and drop
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    
    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', handleDragOver);
        dropZone.addEventListener('drop', handleDrop);
        fileInput.addEventListener('change', handleFileSelect);
    }
}

function switchToTextMode() {
    currentMessageType = 'text';
    
    const textSection = document.getElementById('textSection');
    const mediaSection = document.getElementById('mediaSection');
    const textModeBtn = document.getElementById('textMode');
    const mediaModeBtn = document.getElementById('mediaMode');
    
    if (textSection) textSection.classList.remove('hidden');
    if (mediaSection) mediaSection.classList.add('hidden');
    
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
    
    const textSection = document.getElementById('textSection');
    const mediaSection = document.getElementById('mediaSection');
    const textModeBtn = document.getElementById('textMode');
    const mediaModeBtn = document.getElementById('mediaMode');
    
    if (textSection) textSection.classList.add('hidden');
    if (mediaSection) mediaSection.classList.remove('hidden');
    
    if (textModeBtn) {
        textModeBtn.classList.add('text-muted-foreground', 'hover:text-foreground', 'hover:bg-accent');
        textModeBtn.classList.remove('bg-primary', 'text-primary-foreground', 'shadow-sm');
    }
    if (mediaModeBtn) {
        mediaModeBtn.classList.add('bg-primary', 'text-primary-foreground', 'shadow-sm');
        mediaModeBtn.classList.remove('text-muted-foreground', 'hover:text-foreground', 'hover:bg-accent');
    }
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
    
    if (selectedFiles.length === 0) {
        previewArea.classList.add('hidden');
        return;
    }
    
    previewArea.classList.remove('hidden');
    previewContent.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const fileElement = createFilePreview(file, index);
        previewContent.appendChild(fileElement);
    });
}

function createFilePreview(file, index) {
    const div = document.createElement('div');
        div.className = 'bg-white border border-gray-200 rounded-xl p-4';
    
    const fileIcon = getFileIcon(file.fileType);
    const fileSize = formatFileSize(file.size);
    
    // Se for uma imagem, mostrar a imagem real
    if (file.fileType === 'image') {
        div.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <h5 class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    ${file.name}
                </h5>
                <button onclick="removeFile(${index})" class="text-red-500 hover:text-red-700 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            <div class="space-y-3">
                <div class="relative">
                    <img src="${file.base64}" 
                         alt="${file.name}" 
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
                        ${fileSize} • ${file.fileType}
                    </p>
                </div>
            </div>
        `;
    } else {
        // Para outros tipos de arquivo, mostrar ícone e informações
        div.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="text-2xl">${fileIcon}</div>
                    <div>
                        <p class="font-medium text-gray-900">${file.name}</p>
                        <p class="text-sm text-gray-500">${fileSize} • ${file.fileType}</p>
                    </div>
                </div>
                <button onclick="removeFile(${index})" class="text-red-500 hover:text-red-700 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;
    }
    
    return div;
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

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updatePreview();
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
    initializeMediaFunctionality();
    
    // Only initialize group search if elements exist
    const searchInput = document.getElementById('groups-search');
    const groupsList = document.getElementById('groups-list');
    if (searchInput && groupsList) {
        initializeGroupSearch();
    }
    
    // Override form submission to include media data
    const campaignForm = document.getElementById('campaign-form');
    if (campaignForm) {
        campaignForm.addEventListener('submit', function(e) {
            // Add media data to form before submission
            if (currentMessageType === 'media' && selectedFiles.length > 0) {
                const file = selectedFiles[0];
                
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
            }
        });
    }
});

// Função para reconectar WhatsApp
async function reconnectWhatsApp() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Mostrar loading
    button.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Reconectando...
    `;
    button.disabled = true;
    
    try {
        const response = await fetch('{{ route("mass-sendings.reconnect-whatsapp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Mostrar mensagem de sucesso
            showNotification('WhatsApp reconectado com sucesso! Recarregando página...', 'success');
            
            // Recarregar página após 2 segundos
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification('Erro ao reconectar WhatsApp: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro de conexão. Tente novamente.', 'error');
    } finally {
        // Restaurar botão
        button.innerHTML = originalText;
        button.disabled = false;
    }
}

// Função para regenerar token
async function regenerateToken() {
    if (!confirm('Tem certeza que deseja regenerar o token? Isso irá desconectar o WhatsApp atual e você precisará reconectar.')) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Mostrar loading
    button.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
        </svg>
        Regenerando...
    `;
    button.disabled = true;
    
    try {
        const response = await fetch('{{ route("mass-sendings.regenerate-token") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Token regenerado com sucesso! Reconecte o WhatsApp com o novo token.', 'success');
            
            // Recarregar página após 2 segundos
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification('Erro ao regenerar token: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro de conexão. Tente novamente.', 'error');
    } finally {
        // Restaurar botão
        button.innerHTML = originalText;
        button.disabled = false;
    }
}

// Função para mostrar notificações
function showNotification(message, type = 'info') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-success text-success-foreground' :
        type === 'error' ? 'bg-destructive text-destructive-foreground' :
        'bg-primary text-primary-foreground'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remover após 5 segundos
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection
