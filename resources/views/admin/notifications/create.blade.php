@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Nova Notificação</h1>
            <p class="text-muted-foreground mt-1">Crie e envie notificações para usuárioes</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.notifications.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Create Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-4 sm:space-y-6">
                @csrf

                <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-foreground mb-2">Título</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                   placeholder="Digite o título da notificação..."
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('title')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-foreground mb-2">Mensagem</label>
                            <textarea name="message" id="message" rows="4" 
                                      placeholder="Digite a mensagem da notificação..."
                                      class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-foreground mb-2">Tipo</label>
                                <select name="type" id="type" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    @foreach($types as $notificationType)
                                        <option value="{{ $notificationType }}" {{ old('type') == $notificationType ? 'selected' : '' }}>
                                            {{ ucfirst($notificationType) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Canais</label>
                                <div class="space-y-2">
                                    @foreach($channels as $channel)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="channels[]" id="channel_{{ $channel }}" value="{{ $channel }}" 
                                                   {{ in_array($channel, old('channels', ['database'])) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                            <label for="channel_{{ $channel }}" class="ml-2 text-sm font-medium text-foreground">
                                                {{ ucfirst($channel) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('channels')
                                    <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Destinatários</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Selecionar Usuárioes</label>
                            <div class="max-h-60 overflow-y-auto border border-border rounded-lg p-3">
                                <div class="space-y-2">
                                    @foreach($users as $user)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="user_ids[]" id="user_{{ $user->id }}" value="{{ $user->id }}" 
                                                   {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                            <label for="user_{{ $user->id }}" class="ml-2 text-sm font-medium text-foreground">
                                                {{ $user->name }} ({{ $user->email }})
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('user_ids')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="selectAllUsers()" class="text-sm text-primary hover:text-primary/80">
                                Selecionar Todos
                            </button>
                            <button type="button" onclick="deselectAllUsers()" class="text-sm text-muted-foreground hover:text-foreground">
                                Desmarcar Todos
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Agendamento</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="send_immediately" id="send_immediately" value="1" 
                                       {{ old('send_immediately', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                <label for="send_immediately" class="ml-2 text-sm font-medium text-foreground">
                                    Enviar Imediatamente
                                </label>
                            </div>
                        </div>

                        <div id="schedule_section" class="hidden">
                            <label for="scheduled_at" class="block text-sm font-medium text-foreground mb-2">Data e Hora do Envio</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                                   value="{{ old('scheduled_at') }}"
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('scheduled_at')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Criar Notificação
                    </button>
                    <a href="{{ route('admin.notifications.index') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Help Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Tipos de Notificação</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary/10 text-primary">Info</span>
                        <span class="text-muted-foreground">Informações gerais</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-success/10 text-success">Success</span>
                        <span class="text-muted-foreground">Confirmações e sucessos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-warning/10 text-warning">Warning</span>
                        <span class="text-muted-foreground">Avisos importantes</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-destructive/10 text-destructive">Error</span>
                        <span class="text-muted-foreground">Erros e problemas</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-muted/10 text-muted-foreground">System</span>
                        <span class="text-muted-foreground">Notificações do sistema</span>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Canais de Envio</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-muted/10 text-muted-foreground">Database</span>
                        <span class="text-muted-foreground">Salva no banco de dados</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-muted/10 text-muted-foreground">Email</span>
                        <span class="text-muted-foreground">Envia por e-mail</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-muted/10 text-muted-foreground">Push</span>
                        <span class="text-muted-foreground">Notificação push</span>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Dicas</h3>
                <div class="space-y-2 text-sm text-muted-foreground">
                    <p>• Use títulos claros e objetivos</p>
                    <p>• Mantenha mensagens concisas</p>
                    <p>• Escolha o tipo apropriado</p>
                    <p>• Teste com poucos usuárioes primeiro</p>
                    <p>• Agende para horários apropriados</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAllUsers() {
    const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
}

function deselectAllUsers() {
    const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
}

// Toggle schedule section based on send immediately checkbox
document.getElementById('send_immediately').addEventListener('change', function() {
    const scheduleSection = document.getElementById('schedule_section');
    if (this.checked) {
        scheduleSection.classList.add('hidden');
    } else {
        scheduleSection.classList.remove('hidden');
    }
});

// Initialize schedule section visibility
document.addEventListener('DOMContentLoaded', function() {
    const sendImmediately = document.getElementById('send_immediately');
    const scheduleSection = document.getElementById('schedule_section');
    
    if (sendImmediately.checked) {
        scheduleSection.classList.add('hidden');
    } else {
        scheduleSection.classList.remove('hidden');
    }
});
</script>
@endsection
