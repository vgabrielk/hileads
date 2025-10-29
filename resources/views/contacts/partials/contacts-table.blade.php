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
            <div class="flex items-center justify-end gap-3">
                @if($contact['found'])
                <button onclick="startChat('{{ $contact['phone'] }}', '{{ $contact['user_name'] }}', {{ $contact['id'] ?? 'null' }})" 
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                        title="Iniciar conversa no WhatsApp">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Chat
                </button>
                @endif
                <button onclick="copyToClipboard('{{ $contact['phone'] }}')" 
                        class="text-primary hover:text-primary/80 text-sm font-medium">
                    Copiar
                </button>
            </div>
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

