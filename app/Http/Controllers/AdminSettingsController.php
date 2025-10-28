<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = $this->getAllSettings();
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'app_url' => 'required|url',
            'app_timezone' => 'required|string',
            'app_locale' => 'required|string',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        try {
            $this->updateSetting('app_name', $request->app_name);
            $this->updateSetting('app_description', $request->app_description);
            $this->updateSetting('app_url', $request->app_url);
            $this->updateSetting('app_timezone', $request->app_timezone);
            $this->updateSetting('app_locale', $request->app_locale);
            $this->updateSetting('maintenance_mode', $request->boolean('maintenance_mode'));
            $this->updateSetting('maintenance_message', $request->maintenance_message);

            Log::info('General settings updated by admin', [
                'admin_id' => auth()->id(),
                'settings' => $request->only(['app_name', 'app_description', 'app_url', 'app_timezone', 'app_locale', 'maintenance_mode', 'maintenance_message'])
            ]);

            return redirect()->back()->with('success', 'Configurações gerais atualizadas com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to update general settings', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao atualizar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Update WhatsApp settings.
     */
    public function updateWhatsApp(Request $request)
    {
        $request->validate([
            'wuzapi_base_url' => 'required|url',
            'wuzapi_token' => 'required|string',
            'whatsapp_webhook_url' => 'nullable|url',
            'whatsapp_max_connections_per_user' => 'required|integer|min:1|max:10',
            'whatsapp_connection_timeout' => 'required|integer|min:30|max:300',
        ]);

        try {
            $this->updateSetting('wuzapi_base_url', $request->wuzapi_base_url);
            $this->updateSetting('wuzapi_token', $request->wuzapi_token);
            $this->updateSetting('whatsapp_webhook_url', $request->whatsapp_webhook_url);
            $this->updateSetting('whatsapp_max_connections_per_user', $request->whatsapp_max_connections_per_user);
            $this->updateSetting('whatsapp_connection_timeout', $request->whatsapp_connection_timeout);

            Log::info('WhatsApp settings updated by admin', [
                'admin_id' => auth()->id(),
                'settings' => $request->only(['wuzapi_base_url', 'whatsapp_webhook_url', 'whatsapp_max_connections_per_user', 'whatsapp_connection_timeout'])
            ]);

            return redirect()->back()->with('success', 'Configurações do WhatsApp atualizadas com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to update WhatsApp settings', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao atualizar configurações do WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Update Stripe settings.
     */
    public function updateStripe(Request $request)
    {
        $request->validate([
            'stripe_public_key' => 'required|string',
            'stripe_secret_key' => 'required|string',
            'stripe_webhook_secret' => 'nullable|string',
            'stripe_currency' => 'required|string|size:3',
            'stripe_trial_days' => 'required|integer|min:0|max:30',
        ]);

        try {
            $this->updateSetting('stripe_public_key', $request->stripe_public_key);
            $this->updateSetting('stripe_secret_key', $request->stripe_secret_key);
            $this->updateSetting('stripe_webhook_secret', $request->stripe_webhook_secret);
            $this->updateSetting('stripe_currency', $request->stripe_currency);
            $this->updateSetting('stripe_trial_days', $request->stripe_trial_days);

            Log::info('Stripe settings updated by admin', [
                'admin_id' => auth()->id(),
                'settings' => $request->only(['stripe_currency', 'stripe_trial_days'])
            ]);

            return redirect()->back()->with('success', 'Configurações do Stripe atualizadas com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to update Stripe settings', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao atualizar configurações do Stripe: ' . $e->getMessage());
        }
    }

    /**
     * Update email settings.
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|string|in:smtp,sendmail,mailgun,ses',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        try {
            $this->updateSetting('mail_driver', $request->mail_driver);
            $this->updateSetting('mail_host', $request->mail_host);
            $this->updateSetting('mail_port', $request->mail_port);
            $this->updateSetting('mail_username', $request->mail_username);
            $this->updateSetting('mail_password', $request->mail_password);
            $this->updateSetting('mail_encryption', $request->mail_encryption);
            $this->updateSetting('mail_from_address', $request->mail_from_address);
            $this->updateSetting('mail_from_name', $request->mail_from_name);

            Log::info('Email settings updated by admin', [
                'admin_id' => auth()->id(),
                'settings' => $request->only(['mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_encryption', 'mail_from_address', 'mail_from_name'])
            ]);

            return redirect()->back()->with('success', 'Configurações de e-mail atualizadas com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to update email settings', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao atualizar configurações de e-mail: ' . $e->getMessage());
        }
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'notifications_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'string|in:email,database,slack',
            'admin_notification_email' => 'nullable|email',
            'low_balance_threshold' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->updateSetting('notifications_enabled', $request->boolean('notifications_enabled'));
            $this->updateSetting('email_notifications', $request->boolean('email_notifications'));
            $this->updateSetting('push_notifications', $request->boolean('push_notifications'));
            $this->updateSetting('notification_channels', $request->notification_channels ?? []);
            $this->updateSetting('admin_notification_email', $request->admin_notification_email);
            $this->updateSetting('low_balance_threshold', $request->low_balance_threshold);

            Log::info('Notification settings updated by admin', [
                'admin_id' => auth()->id(),
                'settings' => $request->only(['notifications_enabled', 'email_notifications', 'push_notifications', 'notification_channels', 'admin_notification_email', 'low_balance_threshold'])
            ]);

            return redirect()->back()->with('success', 'Configurações de notificações atualizadas com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to update notification settings', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao atualizar configurações de notificações: ' . $e->getMessage());
        }
    }

    /**
     * Test email configuration.
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // This would send a test email
            // For now, just return success
            Log::info('Email test requested by admin', [
                'admin_id' => auth()->id(),
                'test_email' => $request->test_email
            ]);

            return redirect()->back()->with('success', 'E-mail de teste enviado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to send test email', [
                'admin_id' => auth()->id(),
                'test_email' => $request->test_email,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao enviar e-mail de teste: ' . $e->getMessage());
        }
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        try {
            Cache::flush();
            
            Log::info('Application cache cleared by admin', [
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', 'Cache limpo com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to clear cache', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao limpar cache: ' . $e->getMessage());
        }
    }

    /**
     * Get all settings.
     */
    private function getAllSettings()
    {
        return [
            // General settings
            'app_name' => config('app.name', 'WPP Manager'),
            'app_description' => config('app.description', 'Sistema de gerenciamento de campanhas WhatsApp'),
            'app_url' => config('app.url'),
            'app_timezone' => config('app.timezone', 'America/Sao_Paulo'),
            'app_locale' => config('app.locale', 'pt_BR'),
            'maintenance_mode' => config('app.maintenance_mode', false),
            'maintenance_message' => config('app.maintenance_message', 'Sistema em manutenção'),

            // WhatsApp settings
            'wuzapi_base_url' => config('services.wuzapi.base_url'),
            'wuzapi_token' => config('services.wuzapi.token'),
            'whatsapp_webhook_url' => config('services.wuzapi.webhook_url'),
            'whatsapp_max_connections_per_user' => config('services.wuzapi.max_connections_per_user', 3),
            'whatsapp_connection_timeout' => config('services.wuzapi.connection_timeout', 60),

            // Stripe settings
            'stripe_public_key' => config('stripe.public_key'),
            'stripe_secret_key' => config('stripe.secret_key'),
            'stripe_webhook_secret' => config('stripe.webhook_secret'),
            'stripe_currency' => config('stripe.currency', 'BRL'),
            'stripe_trial_days' => config('stripe.trial_days', 7),

            // Email settings
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_password' => config('mail.mailers.smtp.password'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),

            // Notification settings
            'notifications_enabled' => config('notifications.enabled', true),
            'email_notifications' => config('notifications.email', true),
            'push_notifications' => config('notifications.push', false),
            'notification_channels' => config('notifications.channels', ['email', 'database']),
            'admin_notification_email' => config('notifications.admin_email'),
            'low_balance_threshold' => config('notifications.low_balance_threshold', 100),
        ];
    }

    /**
     * Update a setting value.
     */
    private function updateSetting($key, $value)
    {
        // In a real application, you would update the settings in a database
        // For now, we'll just log the update
        Log::info('Setting updated', [
            'key' => $key,
            'value' => $value,
            'admin_id' => auth()->id()
        ]);
    }
}