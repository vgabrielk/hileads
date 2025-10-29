<!-- Access Status Card -->
@if($accessStatus['is_admin'])
    <div class="relative rounded-lg border border-primary/20 bg-primary/10 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        <div class="flex-1">
            <p class="font-semibold text-primary mb-1">Acesso Administrativo</p>
            <p class="text-sm text-primary opacity-90">Tem acesso completo a todas as funcionalidades do sistema.</p>
        </div>
    </div>
@elseif($accessStatus['has_subscription'] && $accessStatus['current_plan'])
    <div class="relative rounded-lg border border-success/20 bg-success/10 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="flex-1">
            <p class="font-semibold text-success mb-1">Assinatura Ativa</p>
            <p class="text-sm text-success opacity-90">Plano: {{ $accessStatus['current_plan']->name }} - {{ $accessStatus['current_plan']->formatted_price }}</p>
        </div>
        <a href="{{ route('subscriptions.index') }}" class="text-success hover:opacity-70 transition-opacity">
            Gerenciar →
        </a>
    </div>
@else
    <div class="relative rounded-lg border border-warning/20 bg-warning/10 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <div class="flex-1">
            <p class="font-semibold text-warning mb-1">Assinatura Necessária</p>
            <p class="text-sm text-warning opacity-90">Precisa de uma assinatura ativa para acessar a todas as funcionalidades.</p>
        </div>
        <a href="{{ route('plans.index') }}" class="px-4 py-2 text-sm font-medium text-warning-foreground bg-warning hover:bg-warning/90 rounded-lg transition-colors">
            Ver Planos
        </a>
    </div>
@endif

