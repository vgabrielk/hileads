/**
 * Sistema de Carregamento Assíncrono
 * Gerencia o carregamento de dados de forma assíncrona com skeleton loaders
 */

class AsyncLoader {
    constructor() {
        this.pendingRequests = new Map();
        this.cache = new Map();
        this.cacheDuration = 60000; // 1 minuto
    }

    /**
     * Carrega dados de uma URL e atualiza o elemento especificado
     * @param {string} url - URL do endpoint API
     * @param {string|HTMLElement} target - Seletor CSS ou elemento DOM alvo
     * @param {Object} options - Opções de configuração
     */
    async load(url, target, options = {}) {
        const {
            method = 'GET',
            data = null,
            cache = true,
            cacheDuration = this.cacheDuration,
            onSuccess = null,
            onError = null,
            transform = null,
            retries = 2
        } = options;

        const targetElement = typeof target === 'string' 
            ? document.querySelector(target) 
            : target;

        if (!targetElement) {
            console.error(`Elemento alvo não encontrado: ${target}`);
            return;
        }

        // Verificar cache
        const cacheKey = `${method}:${url}:${JSON.stringify(data)}`;
        if (cache && this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < cacheDuration) {
                this.updateElement(targetElement, cached.data, transform);
                if (onSuccess) onSuccess(cached.data);
                return cached.data;
            }
        }

        // Evitar requisições duplicadas
        if (this.pendingRequests.has(cacheKey)) {
            return this.pendingRequests.get(cacheKey);
        }

        const requestPromise = this.executeRequest(url, method, data, retries)
            .then(responseData => {
                // Armazenar no cache
                if (cache) {
                    this.cache.set(cacheKey, {
                        data: responseData,
                        timestamp: Date.now()
                    });
                }

                // Atualizar elemento
                this.updateElement(targetElement, responseData, transform);

                // Callback de sucesso
                if (onSuccess) onSuccess(responseData);

                return responseData;
            })
            .catch(error => {
                console.error('Erro ao carregar dados:', error);
                this.showError(targetElement, error.message);
                
                if (onError) onError(error);
                
                throw error;
            })
            .finally(() => {
                this.pendingRequests.delete(cacheKey);
            });

        this.pendingRequests.set(cacheKey, requestPromise);
        return requestPromise;
    }

    /**
     * Executa a requisição HTTP com retentativas
     */
    async executeRequest(url, method, data, retries) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        };

        if (csrfToken) {
            options.headers['X-CSRF-TOKEN'] = csrfToken;
        }

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        let lastError;
        for (let i = 0; i <= retries; i++) {
            try {
                const response = await fetch(url, options);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                return await response.json();
            } catch (error) {
                lastError = error;
                if (i < retries) {
                    // Aguardar antes de retentar (exponential backoff)
                    await new Promise(resolve => setTimeout(resolve, Math.pow(2, i) * 1000));
                }
            }
        }

        throw lastError;
    }

    /**
     * Atualiza o elemento DOM com os dados recebidos
     */
    updateElement(element, data, transform) {
        let content;
        
        if (transform) {
            content = transform(data);
        } else if (typeof data === 'string') {
            content = data;
        } else if (data.html) {
            content = data.html;
        } else {
            console.warn('Dados recebidos não contêm HTML. Use a opção "transform".');
            return;
        }

        // Adicionar animação de fade
        element.style.opacity = '0';
        
        setTimeout(() => {
            element.innerHTML = content;
            element.style.transition = 'opacity 0.3s ease-in-out';
            element.style.opacity = '1';
            
            // Disparar evento customizado
            element.dispatchEvent(new CustomEvent('async-loaded', { 
                detail: { data } 
            }));
        }, 100);
    }

    /**
     * Mostra mensagem de erro no elemento
     */
    showError(element, message) {
        element.innerHTML = `
            <div class="flex items-center justify-center p-8">
                <div class="text-center">
                    <svg class="w-12 h-12 text-destructive mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm font-medium text-foreground mb-2">Erro ao carregar dados</p>
                    <p class="text-xs text-muted-foreground mb-4">${message}</p>
                    <button onclick="location.reload()" class="px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                        Tentar Novamente
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Limpa o cache
     */
    clearCache(pattern = null) {
        if (pattern) {
            const regex = new RegExp(pattern);
            for (const key of this.cache.keys()) {
                if (regex.test(key)) {
                    this.cache.delete(key);
                }
            }
        } else {
            this.cache.clear();
        }
    }

    /**
     * Carrega múltiplos endpoints em paralelo
     */
    async loadAll(requests) {
        return Promise.all(
            requests.map(req => this.load(req.url, req.target, req.options))
        );
    }
}

// Instância global
window.asyncLoader = new AsyncLoader();

// Função helper para uso simplificado
window.loadAsync = (url, target, options = {}) => {
    return window.asyncLoader.load(url, target, options);
};

// Auto-carregar elementos com atributo data-async-load
document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('[data-async-load]');
    
    elements.forEach(element => {
        const url = element.getAttribute('data-async-load');
        const cache = element.getAttribute('data-async-cache') !== 'false';
        const cacheDuration = parseInt(element.getAttribute('data-async-cache-duration')) || 60000;
        
        if (url) {
            window.asyncLoader.load(url, element, { cache, cacheDuration });
        }
    });
});

