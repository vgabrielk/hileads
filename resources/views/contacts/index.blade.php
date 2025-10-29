@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6" x-data="{ search: '' }">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Contatos</h1>
            <p class="text-muted-foreground mt-1">Faça a gestão a sua lista de contatos</p>
        </div>
        <button onclick="refreshContacts()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Atualizar
        </button>
    </div>

    <!-- Statistics Cards (Carregamento assíncrono) -->
    <div id="stats-cards" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Skeleton loaders -->
        @for($i = 0; $i < 2; $i++)
            <x-skeleton-card />
        @endfor
    </div>

    <!-- Search Bar -->
    <div class="relative">
        <form @submit.prevent="searchContacts()" class="flex gap-4">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Pesquisar contato..." 
                    class="w-full pl-10 pr-4 py-3 bg-card border border-input rounded-lg text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
            </div>
            <button type="submit" class="px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                Procurar
            </button>
            <button type="button" @click="search = ''; searchContacts()" x-show="search" class="px-6 py-3 text-sm font-medium text-foreground bg-secondary hover:bg-secondary/80 rounded-lg transition-colors">
                Limpar
            </button>
        </form>
    </div>

    <!-- Contacts Table -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-secondary border-b border-border">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Contato</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Grupo</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-foreground">Status</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-foreground">Ações</th>
                    </tr>
                </thead>
                <tbody id="contacts-table-body" class="divide-y divide-border" 
                       data-async-load="{{ route('api.contacts.list') }}" 
                       data-async-cache="false">
                    <!-- Skeleton Loaders -->
                    @for($i = 0; $i < 10; $i++)
                        <x-skeleton-table-row />
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function refreshContacts() {
    window.asyncLoader.clearCache();
    window.asyncLoader.load('{{ route('api.contacts.list') }}', '#contacts-table-body', { cache: false });
    loadStats();
}

function searchContacts() {
    const search = document.querySelector('input[type="text"]').value;
    const url = new URL('{{ route('api.contacts.list') }}', window.location.origin);
    if (search) url.searchParams.set('search', search);
    
    window.asyncLoader.load(url.toString(), '#contacts-table-body', { cache: false });
}

function loadStats() {
    // Simular carregamento de estatísticas
    const statsHtml = `
        <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">Total de Grupos</p>
                    <p class="text-3xl font-bold text-foreground" id="groups-count">-</p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-success">↑ Via API Wuzapi</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-card rounded-lg border border-border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">Total de Leads</p>
                    <p class="text-3xl font-bold text-foreground" id="contacts-count">-</p>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-success">↑ Via API Wuzapi</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('stats-cards').innerHTML = statsHtml;
    
    // Carregar dados das estatísticas via API
    fetch('{{ route('api.contacts.list') }}')
        .then(r => r.json())
        .then(data => {
            if (data.data) {
                document.getElementById('groups-count').textContent = data.data.stats?.groups || 0;
                document.getElementById('contacts-count').textContent = new Intl.NumberFormat('pt-PT').format(data.data.stats?.total || 0);
            }
        });
}

// Carregar estatísticas ao iniciar
document.addEventListener('DOMContentLoaded', loadStats);

// Função para copiar texto para a área de transferência
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Mostrar feedback visual
        const btn = event.target;
        const originalText = btn.textContent;
        btn.textContent = 'Copiado!';
        btn.classList.add('text-success');
        setTimeout(() => {
            btn.textContent = originalText;
            btn.classList.remove('text-success');
        }, 2000);
    });
}

// Função para carregar página específica da paginação
function loadPage(page) {
    const url = new URL('{{ route('api.contacts.list') }}', window.location.origin);
    url.searchParams.set('page', page);
    const search = document.querySelector('input[name="search"]')?.value;
    if (search) url.searchParams.set('search', search);
    
    window.asyncLoader.load(url.toString(), '#contacts-table-body', { cache: false });
}

// Função para iniciar chat com contato
function startChat(phone, name, contactId) {
    // Mostrar loading no botão
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Iniciando...
    `;

    // Fazer requisição para iniciar conversa
    fetch('{{ route('chat.start') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            phone: phone,
            name: name,
            contact_id: contactId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirecionar para o chat
            if (window.confirmationModal) {
                window.confirmationModal.show({
                    title: 'Chat Iniciado',
                    message: `Conversa com ${name} iniciada com sucesso!`,
                    type: 'success',
                    confirmText: 'Ir para o Chat',
                    cancelText: 'Continuar Aqui'
                }).then((confirmed) => {
                    if (confirmed) {
                        window.location.href = data.redirect;
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                });
            } else {
                window.location.href = data.redirect;
            }
        } else {
            // Mostrar erro
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            
            if (window.confirmationModal) {
                window.confirmationModal.show({
                    title: 'Erro ao Iniciar Chat',
                    message: data.message || 'Não foi possível iniciar a conversa.',
                    type: 'danger',
                    confirmText: 'OK',
                    cancelText: ''
                }).then(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                });
            } else {
                alert(data.message);
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            }
        }
    })
    .catch(error => {
        console.error('Erro ao iniciar chat:', error);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        
        if (window.confirmationModal) {
            window.confirmationModal.show({
                title: 'Erro',
                message: 'Erro ao iniciar conversa. Por favor, tente novamente.',
                type: 'danger',
                confirmText: 'OK',
                cancelText: ''
            });
        } else {
            alert('Erro ao iniciar conversa. Por favor, tente novamente.');
        }
    });
}
</script>
@endsection

