@forelse($recentContacts as $contact)
    <tr class="hover:bg-accent/50 transition-colors">
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-primary">
                        {{ substr($contact->contact_name ?: $contact->phone_number, 0, 1) }}
                    </span>
                </div>
                <div>
                    <p class="font-medium text-foreground">{{ $contact->contact_name ?: 'Sem nome' }}</p>
                    <p class="text-sm text-muted-foreground">{{ $contact->phone_number }}</p>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 text-foreground">{{ $contact->whatsappGroup->group_name }}</td>
        <td class="px-6 py-4">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($contact->status === 'new') bg-primary/10 text-primary
                @elseif($contact->status === 'contacted') bg-warning/10 text-warning
                @elseif($contact->status === 'interested') bg-success/10 text-success
                @elseif($contact->status === 'not_interested') bg-destructive/10 text-destructive
                @else bg-muted text-muted-foreground @endif">
                @if($contact->status === 'new') Novo
                @elseif($contact->status === 'contacted') Contatado
                @elseif($contact->status === 'interested') Interessado
                @elseif($contact->status === 'not_interested') Não interessado
                @else {{ ucfirst(str_replace('_', ' ', $contact->status)) }}
                @endif
            </span>
        </td>
        <td class="px-6 py-4 text-right">
            <span class="text-muted-foreground text-sm">Via API</span>
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
            <p class="text-xs text-muted-foreground">Extraia contatos dos grupos para começar</p>
        </td>
    </tr>
@endforelse

