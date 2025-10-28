@extends('layouts.app')

@section('content')
<!-- Favicon já está no layout app.blade.php -->
<div class="min-h-screen bg-gray-100">
    <!-- Chat Container - Estilo WhatsApp Web -->
    <div class="h-screen flex overflow-hidden bg-gray-50">
        
        <!-- Sidebar Esquerda - Lista de Conversas -->
        <div class="w-96 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header da Sidebar -->
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Chat WhatsApp</h2>
                    <button onclick="loadConversations()" class="p-2 hover:bg-gray-200 rounded-full transition" title="Atualizar">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="p-3 border-b border-gray-200">
                <div class="relative">
                    <input type="text" id="searchConversations" 
                           placeholder="Pesquisar conversas..." 
                           class="w-full pl-10 pr-4 py-2 bg-gray-100 border-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Lista de Conversas -->
            <div id="conversationsList" class="flex-1 overflow-y-auto">
                <!-- Loader -->
                <div id="conversationsLoader" class="flex items-center justify-center py-10">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-500"></div>
                </div>
                
                <!-- Empty State -->
                <div id="conversationsEmpty" class="hidden flex flex-col items-center justify-center py-10 px-4 text-center">
                    <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Nenhuma conversa encontrada</p>
                    <p class="text-gray-400 text-sm mt-2">As conversas aparecerão aqui quando você trocar mensagens</p>
                </div>

                <!-- Conversas serão carregadas aqui -->
            </div>
        </div>

        <!-- Painel Central - Área de Chat -->
        <div class="flex-1 flex flex-col bg-gray-50">
            <!-- Estado Inicial - Nenhuma conversa selecionada -->
            <div id="noChatSelected" class="flex-1 flex flex-col items-center justify-center bg-white">
                <svg class="w-32 h-32 text-green-100 mb-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
                </svg>
                <h3 class="text-2xl font-light text-gray-600 mb-2">HiLeads Chat WhatsApp</h3>
                <p class="text-gray-400 text-center max-w-md">
                    Selecione uma conversa para começar a trocar mensagens com seus contatos do WhatsApp
                </p>
            </div>

            <!-- Área de Chat Ativa -->
            <div id="chatArea" class="hidden flex-1 flex flex-col">
                <!-- Header do Chat -->
                <div class="bg-gray-100 px-6 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <img id="chatAvatar" class="w-10 h-10 rounded-full hidden" alt="Avatar">
                            <svg id="chatAvatarPlaceholder" class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 id="chatContactName" class="font-semibold text-gray-800"></h3>
                            <p id="chatContactPhone" class="text-xs text-gray-500"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="refreshMessages()" class="p-2 hover:bg-gray-200 rounded-full transition" title="Atualizar mensagens">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Área de Mensagens -->
                <div id="messagesArea" class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#e5ddd5] bg-opacity-50" 
                     style="background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48cGF0dGVybiBpZD0icGF0dGVybiIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiBwYXR0ZXJuVHJhbnNmb3JtPSJyb3RhdGUoNDUpIj48cGF0aCBkPSJNIDAgMCBMIDIwIDIwIiBzdHJva2U9IiNmNWY1ZjUiIHN0cm9rZS13aWR0aD0iMC41IiBmaWxsPSJub25lIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+');">
                    
                    <!-- Loader de Mensagens -->
                    <div id="messagesLoader" class="flex items-center justify-center py-10">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-500"></div>
                    </div>

                    <!-- As mensagens serão carregadas aqui -->
                </div>

                <!-- Input de Mensagem -->
                <div class="bg-gray-100 px-4 py-3 border-t border-gray-200">
                    <div class="flex items-end space-x-3">
                        <!-- Botões de Mídia -->
                        <div class="flex items-center space-x-2">
                            <label for="mediaUpload" class="cursor-pointer p-2 hover:bg-gray-200 rounded-full transition" title="Enviar mídia">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </label>
                            <input type="file" id="mediaUpload" class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx" onchange="handleMediaUpload(this)">
                        </div>

                        <!-- Input de Texto -->
                        <div class="flex-1 relative">
                            <textarea id="messageInput" 
                                      rows="1" 
                                      placeholder="Digite uma mensagem..." 
                                      class="w-full px-4 py-3 bg-white border-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 resize-none max-h-32"
                                      onkeydown="handleMessageKeyDown(event)"></textarea>
                        </div>

                        <!-- Botão Enviar -->
                        <button id="sendButton" 
                                onclick="sendMessage()" 
                                class="p-3 bg-green-500 hover:bg-green-600 text-white rounded-full transition disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Preview de Mídia -->
                    <div id="mediaPreview" class="hidden mt-3 p-3 bg-white rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div id="mediaPreviewIcon" class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"></div>
                                <div>
                                    <p id="mediaPreviewName" class="text-sm font-medium text-gray-700"></p>
                                    <p id="mediaPreviewSize" class="text-xs text-gray-500"></p>
                                </div>
                            </div>
                            <button onclick="cancelMediaUpload()" class="p-1 hover:bg-gray-100 rounded">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <input type="text" id="mediaCaption" placeholder="Adicionar legenda..." class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Status de Envio -->
                    <div id="sendingStatus" class="hidden mt-2 flex items-center space-x-2 text-sm text-gray-600">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-500"></div>
                        <span>Enviando mensagem...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- JavaScript do Chat -->
<script src="{{ asset('js/chat.js') }}"></script>

@endsection

