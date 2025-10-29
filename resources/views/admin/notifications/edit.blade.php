@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Editar Notificação</h1>
            <p class="text-muted-foreground mt-1">Edite as informações desta notificação</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.notifications.show', $notification) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.notifications.update', $notification) }}" class="space-y-4 sm:space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-foreground mb-2">Título</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $notification->title) }}" 
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
                                      class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('message', $notification->message) }}</textarea>
                            @error('message')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-foreground mb-2">Tipo</label>
                                <select name="type" id="type" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    @foreach($types as $notificationType)
                                        <option value="{{ $notificationType }}" {{ old('type', $notification->type) == $notificationType ? 'selected' : '' }}>
                                            {{ ucfirst($notificationType) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-foreground mb-2">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="pending" {{ old('status', $notification->status) == 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="sent" {{ old('status', $notification->status) == 'sent' ? 'selected' : '' }}>Enviado</option>
                                    <option value="failed" {{ old('status', $notification->status) == 'failed' ? 'selected' : '' }}>Falhou</option>
                                    <option value="scheduled" {{ old('status', $notification->status) == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                                </select>
                                @error('status')
                                    <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Canais</label>
                            <div class="space-y-2">
                                @foreach($channels as $channel)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="channels[]" id="channel_{{ $channel }}" value="{{ $channel }}" 
                                               {{ in_array($channel, old('channels', $notification->channels)) ? 'checked' : '' }}
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

                        <div>
                            <label for="scheduled_at" class="block text-sm font-medium text-foreground mb-2">Data e Hora do Envio</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                                   value="{{ old('scheduled_at', $notification->scheduled_at ? $notification->scheduled_at->format('Y-m-d\TH:i') : '') }}"
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
                        Salvar Alterações
                    </button>
                    <a href="{{ route('admin.notifications.show', $notification) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Current Notification Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Atuais</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="font-medium text-muted-foreground">Usuário:</span>
                        <p class="text-foreground">{{ $notification->user->name }}</p>
                        <p class="text-muted-foreground">{{ $notification->user->email }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-muted-foreground">Status Atual:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($notification->status === 'sent') bg-success/10 text-success
                            @elseif($notification->status === 'pending') bg-warning/10 text-warning
                            @elseif($notification->status === 'failed') bg-destructive/10 text-destructive
                            @elseif($notification->status === 'scheduled') bg-primary/10 text-primary
                            @else bg-muted/10 text-muted-foreground @endif">
                            {{ ucfirst($notification->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-muted-foreground">Criado em:</span>
                        <p class="text-foreground">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($notification->sent_at)
                    <div>
                        <span class="font-medium text-muted-foreground">Enviado em:</span>
                        <p class="text-foreground">{{ $notification->sent_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Help -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ajuda</h3>
                <div class="space-y-2 text-sm text-muted-foreground">
                    <p><strong>Título:</strong> Título da notificação.</p>
                    <p><strong>Mensagem:</strong> Conteúdo da notificação.</p>
                    <p><strong>Tipo:</strong> Categoria da notificação.</p>
                    <p><strong>Status:</strong> Estado atual da notificação.</p>
                    <p><strong>Canais:</strong> Como a notificação será enviada.</p>
                    <p><strong>Agendamento:</strong> Quando enviar (opcional).</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
