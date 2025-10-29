@forelse($contacts as $contact)
    <tr class="hover:bg-accent/50 transition-colors">
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                @if($contact['avatar'])
                    <img src="{{ $contact['avatar'] }}" alt="{{ $contact['user_name'] }}" class="w-10 h-10 rounded-full">
                @else
                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-primary">
                            {{ substr($contact['user_name'], 0, 1) }}
                        </span>
                    </div>
                @endif
                <div>
                    <p class="font-medium text-foreground">{{ $contact['user_name'] }}</p>
                    <p class="text-sm text-muted-foreground">{{ $contact['phone'] }}</p>
                </div>
            </div>
        </td>
        <td class="px-6 py-4">
            <span class="text-sm text-foreground">{{ $contact['group_name'] }}</span>
        </td>
        <td class="px-6 py-4">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                {{ $contact['found'] ? 'bg-success/10 text-success' : 'bg-muted text-muted-foreground' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                    {{ $contact['found'] ? 'bg-success' : 'bg-muted-foreground' }}">
                </span>
                {{ $contact['found'] ? 'WhatsApp' : 'Não verificado' }}
            </span>
        </td>
        <td class="px-6 py-4 text-right">
            <button onclick="copyToClipboard('{{ $contact['phone'] }}')" 
                    class="text-primary hover:text-primary/80 text-sm font-medium">
                Copiar
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-6 py-12 text-center">
            <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-foreground mb-1">Nenhum contato encontrado</p>
            <p class="text-xs text-muted-foreground">Conecte seu WhatsApp e sincronize grupos para ver contatos</p>
        </td>
    </tr>
@endforelse

@if(count($contacts) > 0)
    <tr class="bg-secondary">
        <td colspan="4" class="px-6 py-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-muted-foreground">
                    Mostrando {{ count($contacts) }} de {{ number_format($totalContacts) }} contatos
                </span>
                @if($totalPages > 1)
                    <div class="flex gap-2">
                        @if($currentPage > 1)
                            <button onclick="loadPage({{ $currentPage - 1 }})" 
                                    class="px-3 py-1 rounded bg-card hover:bg-accent transition-colors">
                                Anterior
                            </button>
                        @endif
                        <span class="px-3 py-1">
                            Página {{ $currentPage }} de {{ $totalPages }}
                        </span>
                        @if($currentPage < $totalPages)
                            <button onclick="loadPage({{ $currentPage + 1 }})" 
                                    class="px-3 py-1 rounded bg-card hover:bg-accent transition-colors">
                                Próxima
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </td>
    </tr>
@endif

<script>
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

function loadPage(page) {
    const url = new URL('{{ route('api.contacts.list') }}', window.location.origin);
    url.searchParams.set('page', page);
    const search = document.querySelector('input[name="search"]')?.value;
    if (search) url.searchParams.set('search', search);
    
    window.asyncLoader.load(url.toString(), '#contacts-table-body', { cache: false });
}
</script>

