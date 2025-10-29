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
        <p class="text-muted-foreground mt-1">Crie um novo grupo para organizar seus contatos</p>
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
                <h2 class="text-lg font-semibold text-foreground mb-4">Selecionar Contatos</h2>
                
                @if($apiError)
                    <div class="bg-warning/10 border border-warning/20 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-warning mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <p class="text-warning-foreground">Não foi possível carregar contatos da API. Pode adicionar contatos manualmente abaixo.</p>
                        </div>
                    </div>
                @endif

                <!-- API Contacts -->
                @if(!$apiError && count($apiContacts) > 0)
                    <div class="mb-6">
                        <h3 class="text-base font-medium text-foreground mb-3">Contatos da API ({{ count($apiContacts) }})</h3>
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
                    <h3 class="text-base font-medium text-foreground mb-3">Adicionar Contatos Manualmente</h3>
                    <div id="manualContactsContainer">
                        <div class="manual-contact-item flex items-center space-x-4 p-4 border border-border rounded-lg mb-3">
                            <div class="flex-1">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-1">Nome</label>
                                        <input type="text" name="manual_contacts[0][name]" placeholder="Nome do contato"
                                               class="w-full px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-1">Telefone *</label>
                                        <div class="flex gap-2">
                                            <div class="w-32 relative country-selector-wrapper">
                                                <input type="hidden" name="manual_contacts[0][country_code]" value="55" class="country-code-input">
                                                <button type="button" class="country-selector-button w-full px-3 py-2 border border-input rounded-lg bg-background text-foreground flex items-center justify-between hover:border-primary focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                                    <div class="flex items-center gap-2">
                                                        <img src="" alt="" class="country-flag w-5 h-4 object-cover">
                                                        <span class="country-code text-sm">+55</span>
                                                    </div>
                                                    <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                <div class="country-dropdown hidden absolute top-full left-0 mt-1 w-80 max-h-64 overflow-y-auto bg-background border border-border rounded-lg shadow-lg z-50">
                                                    <div class="p-2">
                                                        <input type="text" placeholder="Procurar país..." class="country-search w-full px-3 py-2 border border-input rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                                                    </div>
                                                    <div class="country-list">
                                                        <!-- Países serão carregados aqui -->
                                                    </div>
                                                </div>
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
                        Adicionar Contato
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
                        <p class="text-primary/80 text-sm">Total de contatos selecionados: <span id="totalContacts">0</span></p>
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

// Carregar dados de DDI
async function loadCountriesData() {
    try {
        const response = await fetch('/data/ddi.json');
        countriesData = await response.json();
        initializeAllCountrySelectors();
    } catch (error) {
        console.error('Erro ao carregar dados de países:', error);
        // Fallback para Brasil se houver erro
        countriesData = { '55': { pais: 'Brasil', ddi: 55, img: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Flag_of_Brazil.svg/23px-Flag_of_Brazil.svg.png' } };
        initializeAllCountrySelectors();
    }
}

// Inicializar todos os seletores de país
function initializeAllCountrySelectors() {
    const wrappers = document.querySelectorAll('.country-selector-wrapper');
    wrappers.forEach(wrapper => initializeCountrySelector(wrapper));
}

// Inicializar um seletor específico
function initializeCountrySelector(wrapper) {
    const button = wrapper.querySelector('.country-selector-button');
    const dropdown = wrapper.querySelector('.country-dropdown');
    const countryList = wrapper.querySelector('.country-list');
    const searchInput = wrapper.querySelector('.country-search');
    const hiddenInput = wrapper.querySelector('.country-code-input');
    const flagImg = wrapper.querySelector('.country-flag');
    const codeSpan = wrapper.querySelector('.country-code');
    
    // Popular lista de países
    populateCountryList(countryList);
    
    // Definir país padrão (Brasil)
    const brasilData = countriesData['55'];
    if (brasilData) {
        updateSelectedCountry(wrapper, brasilData);
    }
    
    // Toggle dropdown
    button.addEventListener('click', (e) => {
        e.stopPropagation();
        // Fechar outros dropdowns
        document.querySelectorAll('.country-dropdown').forEach(d => {
            if (d !== dropdown) d.classList.add('hidden');
        });
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            searchInput.focus();
        }
    });
    
    // Busca de países
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const items = countryList.querySelectorAll('.country-item');
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Prevenir fechar ao clicar no dropdown
    dropdown.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

// Popular lista de países no dropdown
function populateCountryList(countryList) {
    countryList.innerHTML = '';
    
    // Ordenar países por nome
    const sortedCountries = Object.entries(countriesData).sort((a, b) => {
        return a[1].pais.localeCompare(b[1].pais);
    });
    
    sortedCountries.forEach(([key, country]) => {
        const item = document.createElement('div');
        item.className = 'country-item flex items-center gap-3 px-4 py-2 hover:bg-accent cursor-pointer transition-colors';
        item.dataset.ddi = country.ddi;
        item.dataset.img = country.img || '';
        item.dataset.name = country.pais;
        
        item.innerHTML = `
            <img src="${country.img || ''}" alt="${country.pais}" class="w-6 h-4 object-cover flex-shrink-0">
            <span class="text-sm text-foreground flex-1">${country.pais}</span>
            <span class="text-sm text-muted-foreground font-medium">+${country.ddi}</span>
        `;
        
        item.addEventListener('click', () => {
            const wrapper = item.closest('.country-selector-wrapper');
            updateSelectedCountry(wrapper, country);
            wrapper.querySelector('.country-dropdown').classList.add('hidden');
            wrapper.querySelector('.country-search').value = '';
            // Resetar busca
            wrapper.querySelectorAll('.country-item').forEach(i => i.style.display = 'flex');
        });
        
        countryList.appendChild(item);
    });
}

// Atualizar país selecionado
function updateSelectedCountry(wrapper, country) {
    const hiddenInput = wrapper.querySelector('.country-code-input');
    const flagImg = wrapper.querySelector('.country-flag');
    const codeSpan = wrapper.querySelector('.country-code');
    
    hiddenInput.value = country.ddi;
    flagImg.src = country.img || '';
    flagImg.alt = country.pais;
    codeSpan.textContent = `+${country.ddi}`;
}

// Fechar dropdowns ao clicar fora
document.addEventListener('click', () => {
    document.querySelectorAll('.country-dropdown').forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
});

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
                    <input type="text" name="manual_contacts[${manualContactIndex}][name]" placeholder="Nome do contato"
                           class="w-full px-3 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-background text-foreground">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Telefone *</label>
                    <div class="flex gap-2">
                        <div class="w-32 relative country-selector-wrapper">
                            <input type="hidden" name="manual_contacts[${manualContactIndex}][country_code]" value="55" class="country-code-input">
                            <button type="button" class="country-selector-button w-full px-3 py-2 border border-input rounded-lg bg-background text-foreground flex items-center justify-between hover:border-primary focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                                <div class="flex items-center gap-2">
                                    <img src="" alt="" class="country-flag w-5 h-4 object-cover">
                                    <span class="country-code text-sm">+55</span>
                                </div>
                                <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="country-dropdown hidden absolute top-full left-0 mt-1 w-80 max-h-64 overflow-y-auto bg-background border border-border rounded-lg shadow-lg z-50">
                                <div class="p-2">
                                    <input type="text" placeholder="Procurar país..." class="country-search w-full px-3 py-2 border border-input rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                                </div>
                                <div class="country-list">
                                    <!-- Países serão carregados aqui -->
                                </div>
                            </div>
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
    
    // Inicializar o seletor de país do novo campo
    const newWrapper = newContact.querySelector('.country-selector-wrapper');
    if (newWrapper && Object.keys(countriesData).length > 0) {
        initializeCountrySelector(newWrapper);
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
