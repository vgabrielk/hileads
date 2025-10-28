@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-foreground">Configurações do Sistema</h1>
            <p class="text-muted-foreground mt-1">Gerencie as configurações globais do sistema</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.settings.clear-cache') }}" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Tem certeza que deseja limpar o cache?')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-warning-foreground bg-warning hover:bg-warning/90 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Limpar Cache
                </button>
            </form>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="bg-card rounded-lg border border-border">
        <div class="border-b border-border">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('general')" id="tab-general" class="tab-button active py-4 px-1 border-b-2 border-primary font-medium text-sm text-primary">
                    Configurações Gerais
                </button>
                <button onclick="showTab('whatsapp')" id="tab-whatsapp" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
                    WhatsApp
                </button>
                <button onclick="showTab('stripe')" id="tab-stripe" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Stripe
                </button>
                <button onclick="showTab('email')" id="tab-email" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
                    E-mail
                </button>
                <button onclick="showTab('notifications')" id="tab-notifications" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Notificações
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- General Settings Tab -->
            <div id="content-general" class="tab-content">
                <form method="POST" action="{{ route('admin.settings.update-general') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="app_name" class="block text-sm font-medium text-foreground mb-2">Nome da Aplicação</label>
                            <input type="text" name="app_name" id="app_name" value="{{ old('app_name', $settings['app_name']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('app_name')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="app_url" class="block text-sm font-medium text-foreground mb-2">URL da Aplicação</label>
                            <input type="url" name="app_url" id="app_url" value="{{ old('app_url', $settings['app_url']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('app_url')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="app_timezone" class="block text-sm font-medium text-foreground mb-2">Fuso Horário</label>
                            <select name="app_timezone" id="app_timezone" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="America/Sao_Paulo" {{ old('app_timezone', $settings['app_timezone']) == 'America/Sao_Paulo' ? 'selected' : '' }}>America/Sao_Paulo</option>
                                <option value="America/New_York" {{ old('app_timezone', $settings['app_timezone']) == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                <option value="Europe/London" {{ old('app_timezone', $settings['app_timezone']) == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                <option value="UTC" {{ old('app_timezone', $settings['app_timezone']) == 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                            @error('app_timezone')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="app_locale" class="block text-sm font-medium text-foreground mb-2">Idioma</label>
                            <select name="app_locale" id="app_locale" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="pt_BR" {{ old('app_locale', $settings['app_locale']) == 'pt_BR' ? 'selected' : '' }}>Português (Brasil)</option>
                                <option value="en_US" {{ old('app_locale', $settings['app_locale']) == 'en_US' ? 'selected' : '' }}>English (US)</option>
                                <option value="es_ES" {{ old('app_locale', $settings['app_locale']) == 'es_ES' ? 'selected' : '' }}>Español</option>
                            </select>
                            @error('app_locale')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="app_description" class="block text-sm font-medium text-foreground mb-2">Descrição da Aplicação</label>
                        <textarea name="app_description" id="app_description" rows="3" 
                                  class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('app_description', $settings['app_description']) }}</textarea>
                        @error('app_description')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" 
                                   {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                            <label for="maintenance_mode" class="ml-2 text-sm font-medium text-foreground">Modo de Manutenção</label>
                        </div>
                    </div>

                    <div>
                        <label for="maintenance_message" class="block text-sm font-medium text-foreground mb-2">Mensagem de Manutenção</label>
                        <textarea name="maintenance_message" id="maintenance_message" rows="2" 
                                  class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('maintenance_message', $settings['maintenance_message']) }}</textarea>
                        @error('maintenance_message')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Configurações Gerais
                        </button>
                    </div>
                </form>
            </div>

            <!-- WhatsApp Settings Tab -->
            <div id="content-whatsapp" class="tab-content hidden">
                <form method="POST" action="{{ route('admin.settings.update-whatsapp') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="wuzapi_base_url" class="block text-sm font-medium text-foreground mb-2">URL Base da Wuzapi</label>
                            <input type="url" name="wuzapi_base_url" id="wuzapi_base_url" value="{{ old('wuzapi_base_url', $settings['wuzapi_base_url']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('wuzapi_base_url')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="wuzapi_token" class="block text-sm font-medium text-foreground mb-2">Token da Wuzapi</label>
                            <input type="password" name="wuzapi_token" id="wuzapi_token" value="{{ old('wuzapi_token', $settings['wuzapi_token']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('wuzapi_token')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="whatsapp_webhook_url" class="block text-sm font-medium text-foreground mb-2">URL do Webhook</label>
                            <input type="url" name="whatsapp_webhook_url" id="whatsapp_webhook_url" value="{{ old('whatsapp_webhook_url', $settings['whatsapp_webhook_url']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('whatsapp_webhook_url')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="whatsapp_max_connections_per_user" class="block text-sm font-medium text-foreground mb-2">Máximo de Conexões por Usuário</label>
                            <input type="number" name="whatsapp_max_connections_per_user" id="whatsapp_max_connections_per_user" 
                                   value="{{ old('whatsapp_max_connections_per_user', $settings['whatsapp_max_connections_per_user']) }}" 
                                   min="1" max="10" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('whatsapp_max_connections_per_user')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="whatsapp_connection_timeout" class="block text-sm font-medium text-foreground mb-2">Timeout de Conexão (segundos)</label>
                            <input type="number" name="whatsapp_connection_timeout" id="whatsapp_connection_timeout" 
                                   value="{{ old('whatsapp_connection_timeout', $settings['whatsapp_connection_timeout']) }}" 
                                   min="30" max="300" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('whatsapp_connection_timeout')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Configurações do WhatsApp
                        </button>
                    </div>
                </form>
            </div>

            <!-- Stripe Settings Tab -->
            <div id="content-stripe" class="tab-content hidden">
                <form method="POST" action="{{ route('admin.settings.update-stripe') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="stripe_public_key" class="block text-sm font-medium text-foreground mb-2">Chave Pública do Stripe</label>
                            <input type="text" name="stripe_public_key" id="stripe_public_key" value="{{ old('stripe_public_key', $settings['stripe_public_key']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('stripe_public_key')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stripe_secret_key" class="block text-sm font-medium text-foreground mb-2">Chave Secreta do Stripe</label>
                            <input type="password" name="stripe_secret_key" id="stripe_secret_key" value="{{ old('stripe_secret_key', $settings['stripe_secret_key']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('stripe_secret_key')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stripe_webhook_secret" class="block text-sm font-medium text-foreground mb-2">Segredo do Webhook</label>
                            <input type="password" name="stripe_webhook_secret" id="stripe_webhook_secret" value="{{ old('stripe_webhook_secret', $settings['stripe_webhook_secret']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('stripe_webhook_secret')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stripe_currency" class="block text-sm font-medium text-foreground mb-2">Moeda</label>
                            <select name="stripe_currency" id="stripe_currency" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="BRL" {{ old('stripe_currency', $settings['stripe_currency']) == 'BRL' ? 'selected' : '' }}>BRL (Real Brasileiro)</option>
                                <option value="USD" {{ old('stripe_currency', $settings['stripe_currency']) == 'USD' ? 'selected' : '' }}>USD (Dólar Americano)</option>
                                <option value="EUR" {{ old('stripe_currency', $settings['stripe_currency']) == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                            </select>
                            @error('stripe_currency')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stripe_trial_days" class="block text-sm font-medium text-foreground mb-2">Dias de Teste Gratuito</label>
                            <input type="number" name="stripe_trial_days" id="stripe_trial_days" 
                                   value="{{ old('stripe_trial_days', $settings['stripe_trial_days']) }}" 
                                   min="0" max="30" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('stripe_trial_days')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Configurações do Stripe
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Settings Tab -->
            <div id="content-email" class="tab-content hidden">
                <form method="POST" action="{{ route('admin.settings.update-email') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mail_driver" class="block text-sm font-medium text-foreground mb-2">Driver de E-mail</label>
                            <select name="mail_driver" id="mail_driver" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="smtp" {{ old('mail_driver', $settings['mail_driver']) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ old('mail_driver', $settings['mail_driver']) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun" {{ old('mail_driver', $settings['mail_driver']) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                <option value="ses" {{ old('mail_driver', $settings['mail_driver']) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                            </select>
                            @error('mail_driver')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_host" class="block text-sm font-medium text-foreground mb-2">Servidor SMTP</label>
                            <input type="text" name="mail_host" id="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('mail_host')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_port" class="block text-sm font-medium text-foreground mb-2">Porta</label>
                            <input type="number" name="mail_port" id="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" 
                                   min="1" max="65535" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('mail_port')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_encryption" class="block text-sm font-medium text-foreground mb-2">Criptografia</label>
                            <select name="mail_encryption" id="mail_encryption" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="" {{ old('mail_encryption', $settings['mail_encryption']) == '' ? 'selected' : '' }}>Nenhuma</option>
                                <option value="tls" {{ old('mail_encryption', $settings['mail_encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                            @error('mail_encryption')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_username" class="block text-sm font-medium text-foreground mb-2">Usuário</label>
                            <input type="text" name="mail_username" id="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('mail_username')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_password" class="block text-sm font-medium text-foreground mb-2">Senha</label>
                            <input type="password" name="mail_password" id="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('mail_password')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_from_address" class="block text-sm font-medium text-foreground mb-2">E-mail de Origem</label>
                            <input type="email" name="mail_from_address" id="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('mail_from_address')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mail_from_name" class="block text-sm font-medium text-foreground mb-2">Nome de Origem</label>
                            <input type="text" name="mail_from_name" id="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('mail_from_name')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <form method="POST" action="{{ route('admin.settings.test-email') }}" class="flex gap-2">
                            @csrf
                            <input type="email" name="test_email" placeholder="E-mail para teste" 
                                   class="px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Testar E-mail
                            </button>
                        </form>

                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Configurações de E-mail
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications Settings Tab -->
            <div id="content-notifications" class="tab-content hidden">
                <form method="POST" action="{{ route('admin.settings.update-notifications') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="notifications_enabled" id="notifications_enabled" value="1" 
                                       {{ old('notifications_enabled', $settings['notifications_enabled']) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                <label for="notifications_enabled" class="ml-2 text-sm font-medium text-foreground">Notificações Habilitadas</label>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="email_notifications" id="email_notifications" value="1" 
                                       {{ old('email_notifications', $settings['email_notifications']) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                <label for="email_notifications" class="ml-2 text-sm font-medium text-foreground">Notificações por E-mail</label>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="push_notifications" id="push_notifications" value="1" 
                                       {{ old('push_notifications', $settings['push_notifications']) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                <label for="push_notifications" class="ml-2 text-sm font-medium text-foreground">Notificações Push</label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="admin_notification_email" class="block text-sm font-medium text-foreground mb-2">E-mail de Notificação do Admin</label>
                            <input type="email" name="admin_notification_email" id="admin_notification_email" value="{{ old('admin_notification_email', $settings['admin_notification_email']) }}" 
                                   class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('admin_notification_email')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="low_balance_threshold" class="block text-sm font-medium text-foreground mb-2">Limite de Saldo Baixo</label>
                            <input type="number" name="low_balance_threshold" id="low_balance_threshold" 
                                   value="{{ old('low_balance_threshold', $settings['low_balance_threshold']) }}" 
                                   min="0" step="0.01" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            @error('low_balance_threshold')
                                <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Canais de Notificação</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="notification_channels[]" id="channel_email" value="email" 
                                       {{ in_array('email', old('notification_channels', $settings['notification_channels'])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                <label for="channel_email" class="ml-2 text-sm font-medium text-foreground">E-mail</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="notification_channels[]" id="channel_database" value="database" 
                                       {{ in_array('database', old('notification_channels', $settings['notification_channels'])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                <label for="channel_database" class="ml-2 text-sm font-medium text-foreground">Banco de Dados</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="notification_channels[]" id="channel_slack" value="slack" 
                                       {{ in_array('slack', old('notification_channels', $settings['notification_channels'])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
                                <label for="channel_slack" class="ml-2 text-sm font-medium text-foreground">Slack</label>
                            </div>
                        </div>
                        @error('notification_channels')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Configurações de Notificações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.add('hidden'));
    
    // Remove active class from all tab buttons
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(button => {
        button.classList.remove('active', 'border-primary', 'text-primary');
        button.classList.add('border-transparent', 'text-muted-foreground');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-primary', 'text-primary');
    activeButton.classList.remove('border-transparent', 'text-muted-foreground');
}
</script>
@endsection
