@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Editar Campanha</h1>
            <p class="text-muted-foreground mt-1">Edite as informações desta campanha</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.campaigns.show', $campaign) }}" 
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
            <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}" class="space-y-4 sm:space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-foreground mb-2">Status</label>
                            <select name="status" id="status" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status', $campaign->status) == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-2">Título</label>
                            <p class="text-foreground">{{ $campaign->title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-2">Tipo de Mensagem</label>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary/10 text-primary">
                                {{ ucfirst($campaign->message_type) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-2">Total de Destinatários</label>
                            <p class="text-foreground font-semibold">{{ number_format($campaign->total_recipients) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Observações</h3>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-foreground mb-2">Notas Administrativas</label>
                        <textarea name="notes" id="notes" rows="4" 
                                  placeholder="Adicione observações sobre esta campanha..."
                                  class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('notes', $campaign->notes) }}</textarea>
                        @error('notes')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salvar Alterações
                    </button>
                    <a href="{{ route('admin.campaigns.show', $campaign) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-muted-foreground bg-muted hover:bg-muted/80 rounded-lg transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Current Campaign Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Atuais</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="font-medium text-muted-foreground">Utilizador:</span>
                        <p class="text-foreground">{{ $campaign->user->name }}</p>
                        <p class="text-muted-foreground">{{ $campaign->user->email }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-muted-foreground">Status Atual:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($campaign->status === 'completed') bg-success/10 text-success
                            @elseif($campaign->status === 'sending') bg-warning/10 text-warning
                            @elseif($campaign->status === 'pending') bg-primary/10 text-primary
                            @elseif($campaign->status === 'failed') bg-destructive/10 text-destructive
                            @elseif($campaign->status === 'cancelled') bg-muted/10 text-muted-foreground
                            @else bg-muted/10 text-muted-foreground @endif">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-muted-foreground">Mensagens Enviadas:</span>
                        <p class="text-foreground">{{ number_format($campaign->sent_count) }} / {{ number_format($campaign->total_recipients) }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-muted-foreground">Criado em:</span>
                        <p class="text-foreground">{{ $campaign->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress Info -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Progresso</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-muted-foreground">Enviadas</span>
                            <span class="text-foreground">{{ number_format($campaign->sent_count) }}</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $campaign->total_recipients > 0 ? ($campaign->sent_count / $campaign->total_recipients) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @if($campaign->failed_count > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-muted-foreground">Falharam</span>
                            <span class="text-destructive">{{ number_format($campaign->failed_count) }}</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-destructive h-2 rounded-full" style="width: {{ $campaign->total_recipients > 0 ? ($campaign->failed_count / $campaign->total_recipients) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Help -->
            <div class="bg-card rounded-lg border border-border p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ajuda</h3>
                <div class="space-y-2 text-sm text-muted-foreground">
                    <p><strong>Status:</strong> Define o estado atual da campanha.</p>
                    <p><strong>Observações:</strong> Adicione notas administrativas sobre a campanha.</p>
                    <p><strong>Cancelar:</strong> Para campanhas pendentes ou em envio.</p>
                    <p><strong>Reiniciar:</strong> Para campanhas que falharam.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
