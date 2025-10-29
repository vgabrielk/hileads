@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Sistema de Notificações</h1>
            <p class="text-muted-foreground mt-1">Faça a gestão notificações para usuárioes</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.notifications.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Notificação
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tipo</label>
                <select name="type" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Todos os tipos</option>
                    @foreach($types as $notificationType)
                        <option value="{{ $notificationType }}" {{ $type == $notificationType ? 'selected' : '' }}>
                            {{ ucfirst($notificationType) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Todos os status</option>
                    @foreach($statuses as $notificationStatus)
                        <option value="{{ $notificationStatus }}" {{ $status == $notificationStatus ? 'selected' : '' }}>
                            {{ ucfirst($notificationStatus) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Por Página</label>
                <select name="per_page" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    @if($notifications->count() > 0)
    <div class="bg-card rounded-lg border border-border p-4">
        <form method="POST" action="{{ route('admin.notifications.bulk-send') }}" id="bulkForm">
            @csrf
            <div class="flex items-center gap-4">
                <button type="button" onclick="selectAll()" class="text-sm text-primary hover:text-primary/80">
                    Selecionar Todos
                </button>
                <button type="button" onclick="deselectAll()" class="text-sm text-muted-foreground hover:text-foreground">
                    Desmarcar Todos
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-warning-foreground bg-warning hover:bg-warning/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Enviar Selecionados
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Notifications Table -->
    <div class="bg-card rounded-lg border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">
                            <input type="checkbox" id="selectAll" onchange="toggleAll()" class="rounded border-border">
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Usuário</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Título</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Tipo</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Canais</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Criado em</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($notifications as $notification)
                        <tr class="hover:bg-muted/30">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="notification_ids[]" value="{{ $notification->id }}" 
                                       class="notification-checkbox rounded border-border">
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-foreground">{{ $notification->user->name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ $notification->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-foreground">{{ Str::limit($notification->title, 50) }}</p>
                                <p class="text-sm text-muted-foreground">{{ Str::limit($notification->message, 80) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($notification->type === 'error') bg-destructive/10 text-destructive
                                    @elseif($notification->type === 'warning') bg-warning/10 text-warning
                                    @elseif($notification->type === 'success') bg-success/10 text-success
                                    @elseif($notification->type === 'info') bg-primary/10 text-primary
                                    @else bg-muted/10 text-muted-foreground @endif">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($notification->status === 'sent') bg-success/10 text-success
                                    @elseif($notification->status === 'pending') bg-warning/10 text-warning
                                    @elseif($notification->status === 'failed') bg-destructive/10 text-destructive
                                    @elseif($notification->status === 'scheduled') bg-primary/10 text-primary
                                    @else bg-muted/10 text-muted-foreground @endif">
                                    {{ ucfirst($notification->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($notification->channels as $channel)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-muted/10 text-muted-foreground">
                                            {{ ucfirst($channel) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-foreground">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                                @if($notification->scheduled_at)
                                    <p class="text-xs text-muted-foreground">Agendado: {{ $notification->scheduled_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.notifications.show', $notification) }}" 
                                       class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-primary hover:bg-primary/10 rounded transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Ver
                                    </a>
                                    @if($notification->status === 'pending' || $notification->status === 'scheduled')
                                        <form method="POST" action="{{ route('admin.notifications.send', $notification) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-success hover:bg-success/10 rounded transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                                Enviar
                                            </button>
                                        </form>
                                    @endif
                                    @if($notification->status === 'scheduled')
                                        <form method="POST" action="{{ route('admin.notifications.cancel', $notification) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Tem certeza que deseja cancelar esta notificação?')"
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-destructive hover:bg-destructive/10 rounded transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-muted-foreground">
                                Nenhuma notificação encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="px-4 py-3 border-t border-border">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    document.getElementById('selectAll').checked = true;
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    document.getElementById('selectAll').checked = false;
}

function toggleAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = selectAllCheckbox.checked);
}

// Update select all checkbox when individual checkboxes change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('notification-checkbox')) {
        const checkboxes = document.querySelectorAll('.notification-checkbox');
        const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        if (checkedBoxes.length === checkboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }
});
</script>
@endsection
