@extends('layouts.app')

@section('title', 'Criar Grupo')

@section('content')
<div class="p-8 space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('groups.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar para Grupos
        </a>
        <h1 class="text-3xl font-bold text-foreground">Criar Novo Grupo</h1>
        <p class="text-muted-foreground mt-1">Crie um novo grupo para organizar seus contactos</p>
    </div>
        <form action="{{ route('groups.store') }}" method="POST" id="groupForm">
            @csrf
            
            <!-- Group Info -->
            <div class="bg-card rounded-lg border border-border p-6 mb-6">
                <h2 class="text-lg font-semibold text-foreground mb-4">Informações do Grupo</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-foreground mb-2">Nome do Grupo *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground">
                        @error('name')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-foreground mb-2">Descrição</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors bg-background text-foreground">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contacts Selection -->
            <div class="bg-card rounded-lg border border-border p-6 mb-6">
                <h2 class="text-lg font-semibold text-foreground mb-4">Selecionar Contactos</h2>
                
                @if($apiError)
                    <div class="bg-warning/10 border border-warning/20 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-warning mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <p class="text-warning-foreground">Não foi possível carregar contactos da API. Pode adicionar contactos manualmente abaixo.</p>
                        </div>
                    </div>
                @endif

                <!-- API Contacts -->
                @if(!$apiError && count($apiContacts) > 0)
                    <div class="mb-6">
                        <h3 class="text-base font-medium text-foreground mb-3">Contactos da API ({{ count($apiContacts) }})</h3>
                        <div class="max-h-64 overflow-y-auto border border-border rounded-lg">
                            <div class="p-4">
                                <label class="flex items-center mb-3">
                                    <input type="checkbox" id="selectAllApi" class="rounded border-input text-primary focus:ring-primary">
                                    <span class="ml-3 font-medium text-foreground">Selecionar Todos</span>
                                </label>
                                <div class="space-y-2">
                                    @foreach($apiContacts as $jid => $contact)
                                        <label class="flex items-center p-2 hover:bg-accent/50 rounded-lg transition-colors">
                                            <input type="checkbox" name="contacts[]" value="{{ $jid }}" 
                                                   class="api-contact-checkbox rounded border-input text-primary focus:ring-primary">
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-medium text-foreground">{{ $contact['PushName'] ?? $contact['name'] ?? 'Sem nome' }}</span>
                                                    <span class="text-sm text-muted-foreground">{{ explode('@', $jid)[0] ?? '' }}</span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Manual Contacts -->
                <div>
                    <h3 class="text-base font-medium text-foreground mb-3">Adicionar Contactos Manualmente</h3>
                    <div id="manualContactsContainer">
                        <div class="manual-contact-item flex items-center space-x-4 p-4 border border-border rounded-lg mb-3">
                            <div class="flex-1">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-1">Nome</label>
                                        <input type="text" name="manual_contacts[0][name]" placeholder="Nome do contacto"
                                               class="w-full px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-1">Telefone *</label>
                                        <div class="flex gap-2">
                                            <div class="w-32 relative">
                                                <select name="manual_contacts[0][country_code]" 
                                                        class="country-code-select w-full px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground appearance-none"
                                                        onchange="updateCountryFlag(this)">
                                                    <option value="55" data-flag="br">🇧🇷 +55</option>
                                                </select>
                                            </div>
                                            <input type="text" name="manual_contacts[0][phone]" placeholder="11999999999" required
                                                   class="flex-1 px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="removeManualContact(this)" class="p-2 text-destructive hover:text-destructive/80 hover:bg-destructive/10 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <button type="button" onclick="addManualContact()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-secondary-foreground bg-secondary hover:bg-secondary/80 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Adicionar Contacto
                    </button>
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-primary/10 border border-primary/20 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-primary font-medium">Resumo do Grupo</p>
                        <p class="text-primary/80 text-sm">Total de contactos selecionados: <span id="totalContacts">0</span></p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('groups.index') }}" class="px-4 py-2 text-muted-foreground hover:text-foreground font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Criar Grupo
                </button>
            </div>
        </form>
</div>

<script>
let manualContactIndex = 1;
let countriesData = {};

// Mapa de códigos de país para flags emoji
const countryFlags = {
    'af': '🇦🇫', 'za': '🇿🇦', 'al': '🇦🇱', 'de': '🇩🇪', 'ad': '🇦🇩', 'ao': '🇦🇴', 'ai': '🇦🇮',
    'ag': '🇦🇬', 'sa': '🇸🇦', 'dz': '🇩🇿', 'ar': '🇦🇷', 'am': '🇦🇲', 'aw': '🇦🇼', 'au': '🇦🇺',
    'at': '🇦🇹', 'az': '🇦🇿', 'bs': '🇧🇸', 'bd': '🇧🇩', 'bb': '🇧🇧', 'bh': '🇧🇭', 'be': '🇧🇪',
    'bz': '🇧🇿', 'bj': '🇧🇯', 'by': '🇧🇾', 'bo': '🇧🇴', 'ba': '🇧🇦', 'bw': '🇧🇼', 'br': '🇧🇷',
    'bn': '🇧🇳', 'bg': '🇧🇬', 'bf': '🇧🇫', 'bi': '🇧🇮', 'bt': '🇧🇹', 'cv': '🇨🇻', 'cm': '🇨🇲',
    'kh': '🇰🇭', 'ca': '🇨🇦', 'td': '🇹🇩', 'cl': '🇨🇱', 'cn': '🇨🇳', 'cy': '🇨🇾', 'co': '🇨🇴',
    'cg': '🇨🇬', 'cd': '🇨🇩', 'kp': '🇰🇵', 'kr': '🇰🇷', 'ci': '🇨🇮', 'cr': '🇨🇷', 'hr': '🇭🇷',
    'cu': '🇨🇺', 'dk': '🇩🇰', 'dj': '🇩🇯', 'eg': '🇪🇬', 'sv': '🇸🇻', 'ae': '🇦🇪', 'ec': '🇪🇨',
    'er': '🇪🇷', 'sk': '🇸🇰', 'si': '🇸🇮', 'es': '🇪🇸', 'ee': '🇪🇪', 'et': '🇪🇹', 'fj': '🇫🇯',
    'ph': '🇵🇭', 'fi': '🇫🇮', 'fr': '🇫🇷', 'ga': '🇬🇦', 'gm': '🇬🇲', 'gh': '🇬🇭', 'ge': '🇬🇪',
    'gi': '🇬🇮', 'gr': '🇬🇷', 'gl': '🇬🇱', 'gp': '🇬🇵', 'gu': '🇬🇺', 'gt': '🇬🇹', 'gy': '🇬🇾',
    'gf': '🇬🇫', 'gn': '🇬🇳', 'gw': '🇬🇼', 'gq': '🇬🇶', 'ht': '🇭🇹', 'hn': '🇭🇳', 'hk': '🇭🇰',
    'hu': '🇭🇺', 'ye': '🇾🇪', 'in': '🇮🇳', 'id': '🇮🇩', 'ir': '🇮🇷', 'iq': '🇮🇶', 'ie': '🇮🇪',
    'is': '🇮🇸', 'il': '🇮🇱', 'it': '🇮🇹', 'jp': '🇯🇵', 'jo': '🇯🇴', 'ki': '🇰🇮', 'xk': '🇽🇰',
    'kw': '🇰🇼', 'la': '🇱🇦', 'ls': '🇱🇸', 'lv': '🇱🇻', 'lb': '🇱🇧', 'lr': '🇱🇷', 'ly': '🇱🇾',
    'li': '🇱🇮', 'lt': '🇱🇹', 'lu': '🇱🇺', 'mo': '🇲🇴', 'mk': '🇲🇰', 'mg': '🇲🇬', 'my': '🇲🇾',
    'mw': '🇲🇼', 'mv': '🇲🇻', 'ml': '🇲🇱', 'mt': '🇲🇹', 'ma': '🇲🇦', 'mq': '🇲🇶', 'mu': '🇲🇺',
    'mr': '🇲🇷', 'mx': '🇲🇽', 'fm': '🇫🇲', 'mz': '🇲🇿', 'md': '🇲🇩', 'mc': '🇲🇨', 'mn': '🇲🇳',
    'me': '🇲🇪', 'mm': '🇲🇲', 'na': '🇳🇦', 'nr': '🇳🇷', 'np': '🇳🇵', 'ni': '🇳🇮', 'ne': '🇳🇪',
    'ng': '🇳🇬', 'nu': '🇳🇺', 'no': '🇳🇴', 'nc': '🇳🇨', 'nz': '🇳🇿', 'om': '🇴🇲', 'nl': '🇳🇱',
    'pw': '🇵🇼', 'ps': '🇵🇸', 'pa': '🇵🇦', 'pg': '🇵🇬', 'pk': '🇵🇰', 'py': '🇵🇾', 'pe': '🇵🇪',
    'pf': '🇵🇫', 'pl': '🇵🇱', 'pt': '🇵🇹', 'qa': '🇶🇦', 'ke': '🇰🇪', 'kg': '🇰🇬', 'gb': '🇬🇧',
    'cf': '🇨🇫', 'cz': '🇨🇿', 're': '🇷🇪', 'ro': '🇷🇴', 'rw': '🇷🇼', 'ru': '🇷🇺', 'ws': '🇼🇸',
    'sm': '🇸🇲', 'pm': '🇵🇲', 'st': '🇸🇹', 'sc': '🇸🇨', 'sn': '🇸🇳', 'sl': '🇸🇱', 'rs': '🇷🇸',
    'sg': '🇸🇬', 'sy': '🇸🇾', 'so': '🇸🇴', 'lk': '🇱🇰', 'sz': '🇸🇿', 'sd': '🇸🇩', 'ss': '🇸🇸',
    'se': '🇸🇪', 'ch': '🇨🇭', 'sr': '🇸🇷', 'tj': '🇹🇯', 'th': '🇹🇭', 'tw': '🇹🇼', 'tz': '🇹🇿',
    'tl': '🇹🇱', 'tg': '🇹🇬', 'tk': '🇹🇰', 'to': '🇹🇴', 'tn': '🇹🇳', 'tm': '🇹🇲', 'tr': '🇹🇷',
    'tv': '🇹🇻', 'ua': '🇺🇦', 'ug': '🇺🇬', 'uy': '🇺🇾', 'uz': '🇺🇿', 'vu': '🇻🇺', 'va': '🇻🇦',
    've': '🇻🇪', 'vn': '🇻🇳', 'wf': '🇼🇫', 'zm': '🇿🇲', 'zw': '🇿🇼', 'us': '🇺🇸'
};

// Carregar dados de DDI
async function loadCountriesData() {
    try {
        const response = await fetch('/data/ddi.json');
        countriesData = await response.json();
        populateAllCountrySelects();
    } catch (error) {
        console.error('Erro ao carregar dados de países:', error);
        // Fallback para Brasil se houver erro
        countriesData = { '55': { pais: 'Brasil', ddi: 55 } };
        populateAllCountrySelects();
    }
}

// Popular todos os selects de país
function populateAllCountrySelects() {
    const selects = document.querySelectorAll('.country-code-select');
    selects.forEach(select => populateCountrySelect(select));
}

// Popular um select específico com os países
function populateCountrySelect(select) {
    const currentValue = select.value || '55';
    select.innerHTML = '';
    
    // Ordenar países por nome
    const sortedCountries = Object.entries(countriesData).sort((a, b) => {
        return a[1].pais.localeCompare(b[1].pais);
    });
    
    sortedCountries.forEach(([key, country]) => {
        const option = document.createElement('option');
        option.value = country.ddi;
        option.textContent = `${getFlagEmoji(country.pais)} +${country.ddi} ${country.pais}`;
        if (country.ddi == currentValue) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}

// Obter emoji de bandeira baseado no nome do país
function getFlagEmoji(countryName) {
    const countryMap = {
        'Brasil': 'br', 'Portugal': 'pt', 'Angola': 'ao', 'Moçambique': 'mz',
        'Cabo Verde': 'cv', 'Estados Unidos': 'us', 'Canadá': 'ca', 'México': 'mx',
        'Argentina': 'ar', 'Chile': 'cl', 'Colômbia': 'co', 'Peru': 'pe',
        'Reino Unido': 'gb', 'França': 'fr', 'Alemanha': 'de', 'Itália': 'it',
        'Espanha': 'es', 'China': 'cn', 'Japão': 'jp', 'Índia': 'in',
        'Austrália': 'au', 'África do Sul': 'za', 'Rússia': 'ru'
    };
    
    const code = countryMap[countryName] || 'br';
    return countryFlags[code] || '🌍';
}

function updateCountryFlag(select) {
    // Função vazia por enquanto, pode ser usada para atualizar UI se necessário
}

// Carregar dados ao iniciar a página
document.addEventListener('DOMContentLoaded', loadCountriesData);

function addManualContact() {
    const container = document.getElementById('manualContactsContainer');
    const newContact = document.createElement('div');
    newContact.className = 'manual-contact-item flex items-center space-x-4 p-4 border border-border rounded-lg mb-3';
    newContact.innerHTML = `
        <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Nome</label>
                    <input type="text" name="manual_contacts[${manualContactIndex}][name]" placeholder="Nome do contacto"
                           class="w-full px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Telefone *</label>
                    <div class="flex gap-2">
                        <div class="w-32 relative">
                            <select name="manual_contacts[${manualContactIndex}][country_code]" 
                                    class="country-code-select w-full px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground appearance-none"
                                    onchange="updateCountryFlag(this)">
                                <option value="55" data-flag="br">🇧🇷 +55</option>
                            </select>
                        </div>
                        <input type="text" name="manual_contacts[${manualContactIndex}][phone]" placeholder="11999999999" required
                               class="flex-1 px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground">
                    </div>
                </div>
            </div>
        </div>
        <button type="button" onclick="removeManualContact(this)" class="p-2 text-destructive hover:text-destructive/80 hover:bg-destructive/10 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(newContact);
    
    // Popular o select de país do novo campo
    const newSelect = newContact.querySelector('.country-code-select');
    if (newSelect && Object.keys(countriesData).length > 0) {
        populateCountrySelect(newSelect);
    }
    
    manualContactIndex++;
    updateTotalContacts();
}

function removeManualContact(button) {
    button.closest('.manual-contact-item').remove();
    updateTotalContacts();
}

function updateTotalContacts() {
    const apiContacts = document.querySelectorAll('.api-contact-checkbox:checked').length;
    const manualContacts = document.querySelectorAll('.manual-contact-item').length;
    const total = apiContacts + manualContacts;
    document.getElementById('totalContacts').textContent = total;
}

// Select all API contacts
document.getElementById('selectAllApi').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.api-contact-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateTotalContacts();
});

// Update total when API contacts are selected
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('api-contact-checkbox')) {
        updateTotalContacts();
    }
});

// Initial update
updateTotalContacts();
</script>
@endsection
