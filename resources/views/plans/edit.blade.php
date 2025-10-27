@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('plans.admin') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar para planos
        </a>
        <h1 class="text-3xl font-bold text-foreground">Editar Plano</h1>
        <p class="text-muted-foreground mt-1">Modifique as configurações do plano "{{ $plan->name }}"</p>
    </div>

    <form method="POST" action="{{ route('plans.update', $plan) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-card rounded-lg border border-border overflow-hidden">
            <div class="p-6 border-b border-border">
                <h2 class="text-lg font-bold text-foreground">Informações Básicas</h2>
                <p class="text-sm text-muted-foreground mt-1">Defina o nome, descrição e preço do plano</p>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-foreground mb-2">
                        Nome do Plano
                        <span class="text-destructive">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $plan->name) }}"
                           class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                           placeholder="Ex: Plano Básico"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-foreground mb-2">
                        Descrição
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none bg-background text-foreground"
                              placeholder="Ex: Ideal para começar com o sistema">{{ old('description', $plan->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price and Interval -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-foreground mb-2">
                            Preço (R$)
                            <span class="text-destructive">*</span>
                        </label>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               value="{{ old('price', $plan->price) }}"
                               step="0.01"
                               min="0"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                               placeholder="29.90"
                               required>
                        @error('price')
                            <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="interval" class="block text-sm font-semibold text-foreground mb-2">
                            Intervalo
                            <span class="text-destructive">*</span>
                        </label>
                        <select id="interval" 
                                name="interval"
                                class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                                required>
                            <option value="monthly" {{ old('interval', $plan->interval) === 'monthly' ? 'selected' : '' }}>Mensal</option>
                            <option value="yearly" {{ old('interval', $plan->interval) === 'yearly' ? 'selected' : '' }}>Anual</option>
                        </select>
                        @error('interval')
                            <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="interval_count" class="block text-sm font-semibold text-foreground mb-2">
                            A cada quantos intervalos
                        </label>
                        <input type="number" 
                               id="interval_count" 
                               name="interval_count" 
                               value="{{ old('interval_count', $plan->interval_count) }}"
                               min="1"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                               placeholder="1">
                        @error('interval_count')
                            <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Limits -->
        <div class="bg-card rounded-lg border border-border overflow-hidden">
            <div class="p-6 border-b border-border">
                <h2 class="text-lg font-bold text-foreground">Limites do Plano</h2>
                <p class="text-sm text-muted-foreground mt-1">Defina os limites de uso para este plano</p>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Max Contacts -->
                    <div>
                        <label for="max_contacts" class="block text-sm font-semibold text-foreground mb-2">
                            Máximo de Contatos
                        </label>
                        <input type="number" 
                               id="max_contacts" 
                               name="max_contacts" 
                               value="{{ old('max_contacts', $plan->max_contacts) }}"
                               min="0"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                               placeholder="1000 (deixe vazio para ilimitado)">
                        <p class="mt-1 text-xs text-muted-foreground">Deixe vazio para ilimitado</p>
                        @error('max_contacts')
                            <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Campaigns -->
                    <div>
                        <label for="max_campaigns" class="block text-sm font-semibold text-foreground mb-2">
                            Máximo de Campanhas
                        </label>
                        <input type="number" 
                               id="max_campaigns" 
                               name="max_campaigns" 
                               value="{{ old('max_campaigns', $plan->max_campaigns) }}"
                               min="0"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                               placeholder="5 (deixe vazio para ilimitado)">
                        <p class="mt-1 text-xs text-muted-foreground">Deixe vazio para ilimitado</p>
                        @error('max_campaigns')
                            <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Mass Sendings -->
                    <div>
                        <label for="max_mass_sendings" class="block text-sm font-semibold text-foreground mb-2">
                            Máximo de Envios em Massa
                        </label>
                        <input type="number" 
                               id="max_mass_sendings" 
                               name="max_mass_sendings" 
                               value="{{ old('max_mass_sendings', $plan->max_mass_sendings) }}"
                               min="0"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                               placeholder="10 (deixe vazio para ilimitado)">
                        <p class="mt-1 text-xs text-muted-foreground">Deixe vazio para ilimitado</p>
                        @error('max_mass_sendings')
                            <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-card rounded-lg border border-border overflow-hidden">
            <div class="p-6 border-b border-border">
                <h2 class="text-lg font-bold text-foreground">Recursos do Plano</h2>
                <p class="text-sm text-muted-foreground mt-1">Liste os recursos incluídos neste plano</p>
            </div>
            
            <div class="p-6">
                <div id="features-container">
                    <!-- Features will be added here dynamically -->
                </div>
                
                <button type="button" 
                        id="add-feature" 
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Adicionar Recurso
                </button>
            </div>
        </div>

        <!-- Settings -->
        <div class="bg-card rounded-lg border border-border overflow-hidden">
            <div class="p-6 border-b border-border">
                <h2 class="text-lg font-bold text-foreground">Configurações</h2>
                <p class="text-sm text-muted-foreground mt-1">Configure o status e destaque do plano</p>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Status -->
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary bg-background border-input rounded focus:ring-primary focus:ring-2">
                        <label for="is_active" class="text-sm font-medium text-foreground">
                            Plano Ativo
                        </label>
                    </div>

                    <!-- Popular -->
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               id="is_popular" 
                               name="is_popular" 
                               value="1"
                               {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary bg-background border-input rounded focus:ring-primary focus:ring-2">
                        <label for="is_popular" class="text-sm font-medium text-foreground">
                            Plano Popular
                        </label>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-semibold text-foreground mb-2">
                            Ordem de Exibição
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', $plan->sort_order) }}"
                               min="0"
                               class="w-full px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
                               placeholder="0">
                        @error('sort_order')
                            <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('plans.admin') }}" class="px-6 py-3 bg-muted text-muted-foreground font-medium rounded-lg hover:bg-muted/80 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-primary text-primary-foreground font-medium rounded-lg hover:bg-primary/90 transition-colors">
                Atualizar Plano
            </button>
        </div>
    </form>
</div>

<script>
let featureIndex = 0;

// Add feature functionality
document.getElementById('add-feature').addEventListener('click', function() {
    const container = document.getElementById('features-container');
    const featureDiv = document.createElement('div');
    featureDiv.className = 'flex items-center space-x-2 mb-2';
    featureDiv.innerHTML = `
        <input type="text" 
               name="features[]" 
               class="flex-1 px-4 py-3 border border-input rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground"
               placeholder="Ex: Até 1.000 contatos">
        <button type="button" 
                onclick="removeFeature(this)" 
                class="p-2 text-destructive hover:text-destructive/80 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(featureDiv);
    featureIndex++;
});

// Remove feature functionality
function removeFeature(button) {
    button.parentElement.remove();
}

// Initialize with existing features
@if($plan->features && count($plan->features) > 0)
    @foreach($plan->features as $feature)
        document.getElementById('add-feature').click();
        const existingInputs = document.querySelectorAll('input[name="features[]"]');
        existingInputs[existingInputs.length - 1].value = '{{ $feature }}';
    @endforeach
@endif

// Initialize with old features if any (for validation errors)
@if(old('features'))
    @foreach(old('features') as $feature)
        document.getElementById('add-feature').click();
        const oldInputs = document.querySelectorAll('input[name="features[]"]');
        oldInputs[oldInputs.length - 1].value = '{{ $feature }}';
    @endforeach
@endif
</script>
@endsection
