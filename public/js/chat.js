// HiLeads WhatsApp Chat - JavaScript Module
// Estado global do chat
let currentConversation = null;
let currentMessages = [];
let selectedMedia = null;
let longPollingInterval = null;
let lastCheckTimestamp = null;

// Inicializar chat ao carregar página
document.addEventListener('DOMContentLoaded', function() {
    loadConversations().then(() => {
        // Verificar se há um parâmetro 'conversation' na URL
        const urlParams = new URLSearchParams(window.location.search);
        const conversationId = urlParams.get('conversation');
        
        if (conversationId) {
            // Tentar abrir a conversa específica
            openConversationById(parseInt(conversationId));
            
            // Limpar o parâmetro da URL sem recarregar
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    });
    startLongPolling();
    setupMessageInput();
    setupSearch();
});

// ==================== CONVERSAS ====================

// Carregar lista de conversas
async function loadConversations() {
    const loader = document.getElementById('conversationsLoader');
    const empty = document.getElementById('conversationsEmpty');
    const list = document.getElementById('conversationsList');
    
    try {
        loader.classList.remove('hidden');
        empty.classList.add('hidden');
        
        const response = await fetch('/chat/conversations');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            renderConversations(data.data);
            empty.classList.add('hidden');
        } else {
            Array.from(list.children).forEach(child => {
                if (child.id !== 'conversationsLoader' && child.id !== 'conversationsEmpty') {
                    child.remove();
                }
            });
            empty.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Erro ao carregar conversas:', error);
        showToast('Erro ao carregar conversas', 'error');
    } finally {
        loader.classList.add('hidden');
    }
}

// Renderizar conversas na lista
function renderConversations(conversations) {
    const list = document.getElementById('conversationsList');
    
    Array.from(list.children).forEach(child => {
        if (child.id !== 'conversationsLoader' && child.id !== 'conversationsEmpty') {
            child.remove();
        }
    });
    
    conversations.forEach(conv => {
        const div = document.createElement('div');
        div.className = `conversation-item px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition ${currentConversation?.id === conv.id ? 'bg-gray-100' : ''}`;
        div.onclick = () => selectConversation(conv);
        
        div.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0">
                    ${conv.avatar_url 
                        ? `<img src="${conv.avatar_url}" class="w-12 h-12 rounded-full" alt="${conv.display_name}">` 
                        : `<svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-semibold text-gray-800 truncate">${conv.display_name}</h4>
                        <span class="text-xs text-gray-500">${conv.last_message_time}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600 truncate flex-1">
                            ${conv.last_message_from_me ? '✓ ' : ''}${conv.last_message_preview || 'Sem mensagens'}
                        </p>
                        ${conv.unread_count > 0 ? `<span class="ml-2 bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">${conv.unread_count}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
        
        list.appendChild(div);
    });
}

// Selecionar conversa
async function selectConversation(conversation) {
    currentConversation = conversation;
    
    // Atualizar UI
    document.getElementById('noChatSelected').classList.add('hidden');
    document.getElementById('chatArea').classList.remove('hidden');
    
    // Atualizar header
    document.getElementById('chatContactName').textContent = conversation.display_name;
    document.getElementById('chatContactPhone').textContent = conversation.formatted_phone;
    
    if (conversation.avatar_url) {
        document.getElementById('chatAvatar').src = conversation.avatar_url;
        document.getElementById('chatAvatar').classList.remove('hidden');
        document.getElementById('chatAvatarPlaceholder').classList.add('hidden');
    } else {
        document.getElementById('chatAvatar').classList.add('hidden');
        document.getElementById('chatAvatarPlaceholder').classList.remove('hidden');
    }
    
    // Marcar conversa selecionada na lista
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('bg-gray-100');
    });
    event.currentTarget?.classList.add('bg-gray-100');
    
    // Carregar mensagens
    await loadMessages(conversation.id);
    
    // Marcar como lida
    await markAsRead(conversation.id);
}

// Abrir conversa específica pelo ID (usado ao vir da lista de contatos)
async function openConversationById(conversationId) {
    try {
        // Buscar a conversa específica na API
        const response = await fetch('/chat/conversations');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            const conversation = data.data.find(c => c.id === conversationId);
            
            if (conversation) {
                // Simular evento para marcar visualmente a conversa
                const conversationElements = document.querySelectorAll('.conversation-item');
                conversationElements.forEach(item => {
                    item.classList.remove('bg-gray-100');
                });
                
                // Abrir a conversa
                await selectConversationDirectly(conversation);
                
                // Marcar visualmente o item na lista
                setTimeout(() => {
                    const selectedItem = Array.from(conversationElements).find(el => 
                        el.textContent.includes(conversation.display_name)
                    );
                    if (selectedItem) {
                        selectedItem.classList.add('bg-gray-100');
                        selectedItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }, 100);
            } else {
                showToast('Conversa não encontrada', 'error');
            }
        }
    } catch (error) {
        console.error('Erro ao abrir conversa:', error);
        showToast('Erro ao abrir conversa', 'error');
    }
}

// Selecionar conversa diretamente (sem depender do evento)
async function selectConversationDirectly(conversation) {
    currentConversation = conversation;
    
    // Atualizar UI
    document.getElementById('noChatSelected').classList.add('hidden');
    document.getElementById('chatArea').classList.remove('hidden');
    
    // Atualizar header
    document.getElementById('chatContactName').textContent = conversation.display_name;
    document.getElementById('chatContactPhone').textContent = conversation.formatted_phone;
    
    if (conversation.avatar_url) {
        document.getElementById('chatAvatar').src = conversation.avatar_url;
        document.getElementById('chatAvatar').classList.remove('hidden');
        document.getElementById('chatAvatarPlaceholder').classList.add('hidden');
    } else {
        document.getElementById('chatAvatar').classList.add('hidden');
        document.getElementById('chatAvatarPlaceholder').classList.remove('hidden');
    }
    
    // Carregar mensagens
    await loadMessages(conversation.id);
    
    // Marcar como lida
    await markAsRead(conversation.id);
}

// ==================== MENSAGENS ====================

// Carregar mensagens de uma conversa
async function loadMessages(conversationId) {
    const messagesArea = document.getElementById('messagesArea');
    const loader = document.getElementById('messagesLoader');
    
    try {
        loader.classList.remove('hidden');
        
        const response = await fetch(`/chat/conversations/${conversationId}/messages`);
        const data = await response.json();
        
        if (data.success) {
            currentMessages = data.data.messages || [];
            renderMessages(currentMessages);
            scrollToBottom();
        } else {
            // Verificar se é erro de sessão desconectada
            if (data.no_session) {
                renderNoSessionError(data.message);
            } else {
                currentMessages = [];
                renderMessages([]);
                showToast(data.message || 'Erro ao carregar mensagens', 'error');
            }
        }
    } catch (error) {
        console.error('Erro ao carregar mensagens:', error);
        currentMessages = [];
        renderMessages([]);
        showToast('Erro ao carregar mensagens', 'error');
    } finally {
        loader.classList.add('hidden');
    }
}

// Renderizar erro de sessão desconectada
function renderNoSessionError(message) {
    const messagesArea = document.getElementById('messagesArea');
    const loader = document.getElementById('messagesLoader');
    
    // Limpar mensagens antigas (exceto loader)
    Array.from(messagesArea.children).forEach(child => {
        if (child.id !== 'messagesLoader') {
            child.remove();
        }
    });
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'flex flex-col items-center justify-center py-20 px-4';
    errorDiv.innerHTML = `
        <svg class="w-24 h-24 text-yellow-500 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">WhatsApp Desconectado</h3>
        <p class="text-gray-600 text-center mb-6 max-w-md">${message}</p>
        <button onclick="window.location.href='/whatsapp'" 
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            Conectar WhatsApp
        </button>
    `;
    
    messagesArea.appendChild(errorDiv);
}

// Renderizar mensagens
function renderMessages(messages) {
    const messagesArea = document.getElementById('messagesArea');
    const loader = document.getElementById('messagesLoader');
    
    // Limpar mensagens antigas (exceto loader)
    Array.from(messagesArea.children).forEach(child => {
        if (child.id !== 'messagesLoader') {
            child.remove();
        }
    });
    
    if (!messages || messages.length === 0) {
        const emptyDiv = document.createElement('div');
        emptyDiv.className = 'flex items-center justify-center h-full text-gray-400';
        emptyDiv.innerHTML = '<p>Nenhuma mensagem ainda. Envie a primeira!</p>';
        messagesArea.appendChild(emptyDiv);
        return;
    }
    
    messages.forEach(msg => {
        const messageDiv = createMessageElement(msg);
        messagesArea.appendChild(messageDiv);
    });
}

// Criar elemento de mensagem
function createMessageElement(message) {
    const div = document.createElement('div');
    const isFromMe = message.from_me;
    
    div.className = `flex ${isFromMe ? 'justify-end' : 'justify-start'}`;
    
    let content = '';
    
    // Mensagem de texto
    if (message.message_type === 'text' || !message.message_type) {
        content = `<p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(message.text)}</p>`;
    }
    // Mensagem de imagem
    else if (message.message_type === 'image') {
        content = `
            <img src="${message.media_link}" class="max-w-xs rounded-lg mb-2 cursor-pointer" onclick="window.open('${message.media_link}', '_blank')" />
            ${message.text ? `<p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(message.text)}</p>` : ''}
        `;
    }
    // Mensagem de vídeo
    else if (message.message_type === 'video') {
        content = `
            <video controls class="max-w-xs rounded-lg mb-2">
                <source src="${message.media_link}" type="video/mp4">
                Seu navegador não suporta vídeo.
            </video>
            ${message.text ? `<p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(message.text)}</p>` : ''}
        `;
    }
    // Mensagem de áudio
    else if (message.message_type === 'audio') {
        content = `
            <audio controls class="max-w-xs">
                <source src="${message.media_link}" type="audio/ogg">
                Seu navegador não suporta áudio.
            </audio>
        `;
    }
    // Mensagem de documento
    else if (message.message_type === 'document') {
        content = `
            <a href="${message.media_link}" target="_blank" class="flex items-center space-x-2 p-3 bg-white bg-opacity-50 rounded-lg hover:bg-opacity-70">
                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium">Documento</p>
                    <p class="text-xs text-gray-600">Clique para abrir</p>
                </div>
            </a>
        `;
    }
    
    const timestamp = new Date(message.timestamp * 1000);
    const timeString = timestamp.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    
    div.innerHTML = `
        <div class="max-w-md lg:max-w-lg xl:max-w-xl ${isFromMe ? 'bg-green-100' : 'bg-white'} rounded-lg shadow-sm px-4 py-2">
            ${content}
            <div class="flex items-center justify-end space-x-1 mt-1">
                <span class="text-xs text-gray-500">${timeString}</span>
                ${isFromMe ? `
                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                ` : ''}
            </div>
        </div>
    `;
    
    return div;
}

// Atualizar mensagens
async function refreshMessages() {
    if (!currentConversation) return;
    await loadMessages(currentConversation.id);
    showToast('Mensagens atualizadas', 'success');
}

// ==================== ENVIO DE MENSAGENS ====================

// Enviar mensagem de texto
async function sendMessage() {
    if (!currentConversation) return;
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    const sendButton = document.getElementById('sendButton');
    const sendingStatus = document.getElementById('sendingStatus');
    
    try {
        sendButton.disabled = true;
        sendingStatus.classList.remove('hidden');
        
        const response = await fetch(`/chat/conversations/${currentConversation.id}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message })
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            await loadMessages(currentConversation.id);
            await loadConversations(); // Atualizar lista
            showToast('Mensagem enviada', 'success');
        } else {
            showToast(data.message || 'Erro ao enviar mensagem', 'error');
        }
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);
        showToast('Erro ao enviar mensagem', 'error');
    } finally {
        sendButton.disabled = false;
        sendingStatus.classList.add('hidden');
    }
}

// Handle de upload de mídia
function handleMediaUpload(input) {
    const file = input.files[0];
    if (!file) return;
    
    selectedMedia = file;
    
    // Mostrar preview
    const preview = document.getElementById('mediaPreview');
    const icon = document.getElementById('mediaPreviewIcon');
    const name = document.getElementById('mediaPreviewName');
    const size = document.getElementById('mediaPreviewSize');
    
    name.textContent = file.name;
    size.textContent = formatFileSize(file.size);
    
    // Ícone baseado no tipo
    const type = file.type.split('/')[0];
    let iconHtml = '';
    
    if (type === 'image') {
        const reader = new FileReader();
        reader.onload = (e) => {
            icon.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded">`;
        };
        reader.readAsDataURL(file);
    } else if (type === 'video') {
        iconHtml = '<svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>';
        icon.innerHTML = iconHtml;
    } else if (type === 'audio') {
        iconHtml = '<svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>';
        icon.innerHTML = iconHtml;
    } else {
        iconHtml = '<svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>';
        icon.innerHTML = iconHtml;
    }
    
    preview.classList.remove('hidden');
    
    // Preparar para envio
    showMediaSendButton();
}

// Cancelar upload de mídia
function cancelMediaUpload() {
    selectedMedia = null;
    document.getElementById('mediaUpload').value = '';
    document.getElementById('mediaPreview').classList.add('hidden');
    document.getElementById('mediaCaption').value = '';
}

// Enviar mídia
async function sendMediaMessage() {
    if (!currentConversation || !selectedMedia) return;
    
    const caption = document.getElementById('mediaCaption').value;
    const sendingStatus = document.getElementById('sendingStatus');
    
    try {
        sendingStatus.classList.remove('hidden');
        
        const formData = new FormData();
        formData.append('media_file', selectedMedia);
        formData.append('caption', caption);
        
        // Determinar tipo de mídia
        const type = selectedMedia.type.split('/')[0];
        const mediaType = type === 'application' ? 'document' : type;
        formData.append('media_type', mediaType);
        
        const response = await fetch(`/chat/conversations/${currentConversation.id}/send-media`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            cancelMediaUpload();
            await loadMessages(currentConversation.id);
            await loadConversations();
            showToast('Mídia enviada', 'success');
        } else {
            showToast(data.message || 'Erro ao enviar mídia', 'error');
        }
    } catch (error) {
        console.error('Erro ao enviar mídia:', error);
        showToast('Erro ao enviar mídia', 'error');
    } finally {
        sendingStatus.classList.add('hidden');
    }
}

function showMediaSendButton() {
    const sendButton = document.getElementById('sendButton');
    sendButton.onclick = sendMediaMessage;
    sendButton.disabled = false;
}

// Marcar como lida
async function markAsRead(conversationId) {
    try {
        await fetch(`/chat/conversations/${conversationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    } catch (error) {
        console.error('Erro ao marcar como lida:', error);
    }
}

// ==================== LONG POLLING ====================

// Iniciar long polling para novas mensagens
function startLongPolling() {
    // Verificar a cada 5 segundos
    longPollingInterval = setInterval(checkNewMessages, 5000);
}

// Verificar novas mensagens
async function checkNewMessages() {
    try {
        const params = lastCheckTimestamp ? `?last_check=${lastCheckTimestamp}` : '';
        const response = await fetch(`/chat/check-new-messages${params}`);
        const data = await response.json();
        
        if (data.success && data.data.has_updates) {
            // Atualizar timestamp
            lastCheckTimestamp = data.data.timestamp;
            
            // Recarregar conversas
            await loadConversations();
            
            // Se estiver vendo uma conversa atualizada, recarregar mensagens
            if (currentConversation) {
                const updated = data.data.conversations.find(c => c.id === currentConversation.id);
                if (updated && !updated.last_message_from_me) {
                    await loadMessages(currentConversation.id);
                    showToast('Nova mensagem recebida', 'info');
                }
            }
        }
    } catch (error) {
        console.error('Erro no long polling:', error);
    }
}

// ==================== HELPERS ====================

// Setup do input de mensagem
function setupMessageInput() {
    const input = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    
    input.addEventListener('input', function() {
        // Auto-resize
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        
        // Habilitar/desabilitar botão enviar
        sendButton.disabled = !this.value.trim();
        
        // Resetar ação do botão enviar para texto
        if (!selectedMedia) {
            sendButton.onclick = sendMessage;
        }
    });
}

// Handle de Enter no input
function handleMessageKeyDown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        if (selectedMedia) {
            sendMediaMessage();
        } else {
            sendMessage();
        }
    }
}

// Setup da busca de conversas
function setupSearch() {
    const searchInput = document.getElementById('searchConversations');
    
    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        const items = document.querySelectorAll('.conversation-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(term) ? 'block' : 'none';
        });
    });
}

// Scroll para o final das mensagens
function scrollToBottom() {
    const messagesArea = document.getElementById('messagesArea');
    setTimeout(() => {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }, 100);
}

// Formatar tamanho de arquivo
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Mostrar toast notification
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    toast.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 transform transition-all duration-300 translate-x-0`;
    toast.innerHTML = `
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span>${message}</span>
    `;
    
    container.appendChild(toast);
    
    // Remover após 3 segundos
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

