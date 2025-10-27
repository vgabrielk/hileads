@extends('layouts.app')

@section('title', 'Envio de Mídias - HiLeads')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                📱 Envio de Mídias WhatsApp
            </h1>
            <p class="text-lg text-gray-600">
                Envie textos, imagens, vídeos, áudios e documentos de forma simples e intuitiva
            </p>
        </div>

        <!-- Main Interface -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Message Composer -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex-1">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            📞 Número do WhatsApp
                        </label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone"
                            placeholder="Ex: 5511999999999"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            required
                        >
                    </div>
                    <div class="flex-1">
                        <label for="messageId" class="block text-sm font-medium text-gray-700 mb-2">
                            🆔 ID da Mensagem (opcional)
                        </label>
                        <input 
                            type="text" 
                            id="messageId" 
                            name="messageId"
                            placeholder="Deixe vazio para gerar automaticamente"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                    </div>
                </div>
            </div>

            <!-- Media Upload Area -->
            <div class="p-6">
                <!-- Upload Buttons -->
                <div class="flex flex-wrap gap-3 mb-6">
                    <button 
                        type="button" 
                        id="uploadImage" 
                        class="flex items-center px-4 py-3 bg-green-100 hover:bg-green-200 text-green-700 rounded-xl transition-all hover:scale-105"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                        📷 Imagem
                    </button>
                    
                    <button 
                        type="button" 
                        id="uploadVideo" 
                        class="flex items-center px-4 py-3 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-xl transition-all hover:scale-105"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                        </svg>
                        🎥 Vídeo
                    </button>
                    
                    <button 
                        type="button" 
                        id="uploadAudio" 
                        class="flex items-center px-4 py-3 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-xl transition-all hover:scale-105"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 12a7.971 7.971 0 00-1.343-4.243 1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        🎤 Áudio
                    </button>
                    
                    <button 
                        type="button" 
                        id="uploadDocument" 
                        class="flex items-center px-4 py-3 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-xl transition-all hover:scale-105"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        📄 Documento
                    </button>
                </div>

                <!-- Drag & Drop Area -->
                <div 
                    id="dropZone" 
                    class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-all cursor-pointer"
                >
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
            </div>

            <!-- Message Editor -->
            <div class="p-6 border-t border-gray-200">
                <label for="messageText" class="block text-sm font-medium text-gray-700 mb-2">
                    💬 Mensagem de Texto (opcional)
                </label>
                <textarea 
                    id="messageText" 
                    name="messageText"
                    rows="4"
                    placeholder="Digite sua mensagem aqui..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                ></textarea>
            </div>

            <!-- Preview Area -->
            <div id="previewArea" class="p-6 border-t border-gray-200 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Preview</h3>
                <div id="previewContent" class="space-y-4">
                    <!-- Preview content will be inserted here -->
                </div>
            </div>

            <!-- Send Button -->
            <div class="p-6 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button 
                            type="button" 
                            id="clearAll" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
                        >
                            🗑️ Limpar Tudo
                        </button>
                        <button 
                            type="button" 
                            id="addMore" 
                            class="px-4 py-2 text-blue-600 hover:text-blue-800 transition-colors"
                        >
                            ➕ Adicionar Mais
                        </button>
                    </div>
                    
                    <button 
                        type="button" 
                        id="sendMessage" 
                        class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                        </svg>
                        <span>Enviar Mensagem</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Batch Sending Section -->
        <div class="mt-8 bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    📤 Envio em Lote
                </h2>
                <p class="text-gray-600">
                    Envie a mesma mensagem para múltiplos contatos
                </p>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label for="batchContacts" class="block text-sm font-medium text-gray-700 mb-2">
                            📞 Contatos (um por linha)
                        </label>
                        <textarea 
                            id="batchContacts" 
                            rows="4"
                            placeholder="5511999999999&#10;5511888888888&#10;5511777777777"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        ></textarea>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            <span id="contactCount">0</span> contatos adicionados
                        </div>
                        <button 
                            type="button" 
                            id="sendBatch" 
                            class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            <span>Enviar em Lote</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="statusMessages" class="mt-6 space-y-2">
            <!-- Status messages will appear here -->
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-8 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-lg font-semibold text-gray-900">Enviando mensagem...</p>
        <p class="text-sm text-gray-600">Por favor, aguarde</p>
    </div>
</div>

<script>
// Global variables
let selectedFiles = [];
let currentMessageType = 'text';

// Initialize the interface
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    updateContactCount();
});

function initializeEventListeners() {
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
    
    // Action buttons
    document.getElementById('sendMessage').addEventListener('click', sendMessage);
    document.getElementById('sendBatch').addEventListener('click', sendBatch);
    document.getElementById('clearAll').addEventListener('click', clearAll);
    document.getElementById('addMore').addEventListener('click', addMoreFiles);
    
    // Contact count update
    document.getElementById('batchContacts').addEventListener('input', updateContactCount);
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
        showStatus('❌ Arquivo muito grande. Máximo 50MB.', 'error');
        return false;
    }
    return true;
}

function convertToBase64(file) {
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
        
        selectedFiles.push(fileData);
        updatePreview();
        showStatus(`✅ ${file.name} adicionado com sucesso!`, 'success');
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
    div.className = 'flex items-center justify-between p-4 bg-gray-50 rounded-xl';
    
    const fileIcon = getFileIcon(file.fileType);
    const fileSize = formatFileSize(file.size);
    
    div.innerHTML = `
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
    `;
    
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

function clearAll() {
    selectedFiles = [];
    document.getElementById('messageText').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('messageId').value = '';
    updatePreview();
    showStatus('🗑️ Tudo limpo!', 'info');
}

function addMoreFiles() {
    document.getElementById('fileInput').click();
}

function updateContactCount() {
    const textarea = document.getElementById('batchContacts');
    const contacts = textarea.value.split('\n').filter(contact => contact.trim() !== '');
    document.getElementById('contactCount').textContent = contacts.length;
}

async function sendMessage() {
    const phone = document.getElementById('phone').value.trim();
    const messageId = document.getElementById('messageId').value.trim() || generateMessageId();
    const messageText = document.getElementById('messageText').value.trim();
    
    if (!phone) {
        showStatus('❌ Por favor, insira um número de telefone.', 'error');
        return;
    }
    
    if (selectedFiles.length === 0 && !messageText) {
        showStatus('❌ Por favor, adicione um arquivo ou digite uma mensagem.', 'error');
        return;
    }
    
    showLoading(true);
    
    try {
        // Send text message if there's text
        if (messageText) {
            await sendTextMessage(phone, messageText, messageId);
        }
        
        // Send media files
        for (const file of selectedFiles) {
            await sendMediaMessage(phone, file, messageId);
        }
        
        showStatus('✅ Mensagem enviada com sucesso!', 'success');
        clearAll();
    } catch (error) {
        showStatus(`❌ Erro ao enviar mensagem: ${error.message}`, 'error');
    } finally {
        showLoading(false);
    }
}

async function sendBatch() {
    const contactsText = document.getElementById('batchContacts').value.trim();
    const messageText = document.getElementById('messageText').value.trim();
    
    if (!contactsText) {
        showStatus('❌ Por favor, insira pelo menos um contato.', 'error');
        return;
    }
    
    if (selectedFiles.length === 0 && !messageText) {
        showStatus('❌ Por favor, adicione um arquivo ou digite uma mensagem.', 'error');
        return;
    }
    
    const contacts = contactsText.split('\n').filter(contact => contact.trim() !== '');
    
    showLoading(true);
    
    try {
        const batchData = {
            contacts: contacts,
            type: selectedFiles.length > 0 ? selectedFiles[0].fileType : 'text',
            data: selectedFiles.length > 0 ? {
                [selectedFiles[0].fileType === 'image' ? 'Image' : 
                 selectedFiles[0].fileType === 'video' ? 'Video' :
                 selectedFiles[0].fileType === 'audio' ? 'Audio' : 'Document']: selectedFiles[0].base64,
                ...(selectedFiles[0].fileType === 'image' && messageText ? { Caption: messageText } : {}),
                ...(selectedFiles[0].fileType === 'video' && messageText ? { Caption: messageText } : {}),
                ...(selectedFiles[0].fileType === 'document' ? { FileName: selectedFiles[0].name } : {})
            } : { Body: messageText }
        };
        
        const response = await fetch('/media/send/batch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(batchData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showStatus(`✅ Envio em lote concluído: ${result.stats.success} sucessos, ${result.stats.errors} erros`, 'success');
        } else {
            showStatus(`❌ Erro no envio em lote: ${result.message}`, 'error');
        }
    } catch (error) {
        showStatus(`❌ Erro ao enviar em lote: ${error.message}`, 'error');
    } finally {
        showLoading(false);
    }
}

async function sendTextMessage(phone, text, messageId) {
    const response = await fetch('/media/send/text', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            phone: phone,
            body: text,
            id: messageId
        })
    });
    
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message);
    }
    return result;
}

async function sendMediaMessage(phone, file, messageId) {
    const endpoint = `/media/send/${file.fileType}`;
    const data = {
        phone: phone,
        [file.fileType]: file.base64,
        id: messageId
    };
    
    if (file.fileType === 'image' || file.fileType === 'video') {
        const caption = document.getElementById('messageText').value.trim();
        if (caption) {
            data.caption = caption;
        }
    }
    
    if (file.fileType === 'document') {
        data.fileName = file.name;
    }
    
    const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    });
    
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message);
    }
    return result;
}

function generateMessageId() {
    return 'msg_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    if (show) {
        overlay.classList.remove('hidden');
    } else {
        overlay.classList.add('hidden');
    }
}

function showStatus(message, type) {
    const statusContainer = document.getElementById('statusMessages');
    const statusDiv = document.createElement('div');
    
    const bgColor = {
        'success': 'bg-green-100 text-green-800',
        'error': 'bg-red-100 text-red-800',
        'info': 'bg-blue-100 text-blue-800'
    }[type] || 'bg-gray-100 text-gray-800';
    
    statusDiv.className = `p-4 rounded-xl ${bgColor} font-medium`;
    statusDiv.textContent = message;
    
    statusContainer.appendChild(statusDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (statusDiv.parentNode) {
            statusDiv.parentNode.removeChild(statusDiv);
        }
    }, 5000);
}
</script>
@endsection
