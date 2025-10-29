<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MassSendingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WuzapiToolsController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard API Endpoints (async loading)
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');
        Route::get('/access-status', [DashboardController::class, 'getAccessStatus'])->name('access-status');
        Route::get('/recent-connections', [DashboardController::class, 'getRecentConnections'])->name('recent-connections');
        Route::get('/recent-groups', [DashboardController::class, 'getRecentGroups'])->name('recent-groups');
        Route::get('/recent-contacts', [DashboardController::class, 'getRecentContacts'])->name('recent-contacts');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/regenerate-token', [ProfileController::class, 'regenerateToken'])->name('profile.regenerate-token');

    // Wuzapi tools
    Route::get('/whatsapp/status', [WuzapiToolsController::class, 'status'])->name('whatsapp.status');
    Route::get('/whatsapp/contacts', [WuzapiToolsController::class, 'contacts'])->name('whatsapp.contacts');
    Route::post('/whatsapp/tools/user-info', [WuzapiToolsController::class, 'userInfo'])->name('whatsapp.user-info');
    Route::post('/whatsapp/tools/user-check', [WuzapiToolsController::class, 'userCheck'])->name('whatsapp.user-check');
    Route::post('/whatsapp/tools/presence', [WuzapiToolsController::class, 'setPresence'])->name('whatsapp.presence');
    Route::post('/whatsapp/tools/avatar', [WuzapiToolsController::class, 'getAvatar'])->name('whatsapp.avatar');
    Route::post('/whatsapp/tools/send-text', [WuzapiToolsController::class, 'sendText'])->name('whatsapp.send-text');
    Route::post('/whatsapp/tools/mark-read', [WuzapiToolsController::class, 'markRead'])->name('whatsapp.mark-read');
    Route::post('/whatsapp/tools/react', [WuzapiToolsController::class, 'react'])->name('whatsapp.react');
    Route::post('/whatsapp/tools/delete', [WuzapiToolsController::class, 'deleteMessage'])->name('whatsapp.delete');

    // Admin Web Pages
    Route::middleware('admin')->group(function () {
        Route::get('/admin/wuzapi-users-page', [AdminUserController::class, 'wuzapiUsersPage'])->name('admin.wuzapi-users-page');
        
        // User Management
        Route::resource('users', App\Http\Controllers\UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
        Route::post('admin/users/{user}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::post('admin/users/{user}/regenerate-token', [App\Http\Controllers\UserController::class, 'regenerateToken'])->name('admin.users.regenerate-token');
    });

    // WhatsApp Connections - Rotas específicas primeiro
    Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index')->middleware('subscription.security');
    Route::get('/whatsapp/connect', [WhatsAppController::class, 'connect'])->name('whatsapp.connect')->middleware('subscription.security');
    Route::get('/whatsapp/connect-flow', [WhatsAppController::class, 'showConnectFlow'])->name('whatsapp.connect-flow')->middleware('subscription.security');
    Route::get('/whatsapp/status', [WhatsAppController::class, 'checkStatus'])->name('whatsapp.status')->middleware('subscription.security');
    Route::post('/whatsapp/disconnect', [WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect')->middleware('subscription.security');
    Route::post('/whatsapp/logout', [WhatsAppController::class, 'logout'])->name('whatsapp.logout')->middleware('subscription.security');
    
    // WhatsApp API Endpoints for Connect Flow
    Route::post('/whatsapp/connect-session', [WhatsAppController::class, 'connectSession'])->name('whatsapp.connect-session')->middleware('subscription.security');
    Route::get('/whatsapp/get-qr', [WhatsAppController::class, 'getQR'])->name('whatsapp.get-qr')->middleware('subscription.security');
    Route::get('/whatsapp/check-status', [WhatsAppController::class, 'getStatus'])->name('whatsapp.check-status')->middleware('subscription.security');
    
    // WhatsApp Routes with parameters - devem vir por último
    Route::get('/whatsapp/{whatsapp}', [WhatsAppController::class, 'show'])->name('whatsapp.show')->middleware('subscription.security');
    Route::delete('/whatsapp/{whatsapp}', [WhatsAppController::class, 'destroy'])->name('whatsapp.destroy')->middleware('subscription.security');
    
    // Contacts (apenas visualização via API)
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('api/contacts', [ContactController::class, 'getContacts'])->name('api.contacts.list');
    

    // Mass Sendings
    Route::resource('mass-sendings', MassSendingController::class)->middleware('subscription.security');
    Route::post('mass-sendings/{massSending}/start', [MassSendingController::class, 'start'])->name('mass-sendings.start')->middleware('subscription.security');
    Route::post('mass-sendings/{massSending}/pause', [MassSendingController::class, 'pause'])->name('mass-sendings.pause')->middleware('subscription.security');
    Route::post('mass-sendings/{massSending}/resume', [MassSendingController::class, 'resume'])->name('mass-sendings.resume')->middleware('subscription.security');
    Route::get('mass-sendings/{massSending}/progress', [MassSendingController::class, 'progress'])->name('mass-sendings.progress')->middleware('subscription.security');
    
    // Mass sending inline editing and resend
    // Nota: edit e update já existem via resource, apenas sobrescrevendo métodos customizados
    Route::put('mass-sendings/{massSending}/update-inline', [MassSendingController::class, 'updateInline'])->name('mass-sendings.update-inline');
    Route::post('mass-sendings/{massSending}/resend', [MassSendingController::class, 'resend'])->name('mass-sendings.resend');
    Route::get('mass-sendings/{massSending}/edit-data', [MassSendingController::class, 'getEditData'])->name('mass-sendings.edit-data');
    
    // WhatsApp connection management
    Route::post('mass-sendings/regenerate-token', [MassSendingController::class, 'regenerateToken'])->name('mass-sendings.regenerate-token');
    Route::post('mass-sendings/reconnect-whatsapp', [MassSendingController::class, 'reconnectWhatsApp'])->name('mass-sendings.reconnect-whatsapp');

    // Media Message Routes
    Route::prefix('media')->name('media.')->group(function () {
        Route::post('send/text', [App\Http\Controllers\MediaMessageController::class, 'sendText'])->name('send.text');
        Route::post('send/image', [App\Http\Controllers\MediaMessageController::class, 'sendImage'])->name('send.image');
        Route::post('send/audio', [App\Http\Controllers\MediaMessageController::class, 'sendAudio'])->name('send.audio');
        Route::post('send/document', [App\Http\Controllers\MediaMessageController::class, 'sendDocument'])->name('send.document');
        Route::post('send/video', [App\Http\Controllers\MediaMessageController::class, 'sendVideo'])->name('send.video');
        Route::post('send/batch', [App\Http\Controllers\MediaMessageController::class, 'sendBatch'])->name('send.batch');
        Route::post('convert-base64', [App\Http\Controllers\MediaMessageController::class, 'convertToBase64'])->name('convert.base64');
    });

    // Chat Routes (WhatsApp Chat Module)
    Route::prefix('chat')->name('chat.')->middleware('subscription.security')->group(function () {
        Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
        Route::post('/start', [App\Http\Controllers\ChatController::class, 'startConversation'])->name('start');
        Route::get('/conversations', [App\Http\Controllers\ChatController::class, 'getConversations'])->name('conversations');
        Route::get('/conversations/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
        Route::post('/conversations/{conversation}/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');
        Route::post('/conversations/{conversation}/send-media', [App\Http\Controllers\ChatController::class, 'sendMedia'])->name('send-media');
        Route::post('/conversations/{conversation}/mark-read', [App\Http\Controllers\ChatController::class, 'markAsRead'])->name('mark-read');
        Route::get('/check-new-messages', [App\Http\Controllers\ChatController::class, 'checkNewMessages'])->name('check-new');
    });

    // Message History Routes (temporarily disabled - MessageController not found)
    // Route::prefix('messages')->name('messages.')->group(function () {
    //     Route::get('/', [App\Http\Controllers\MessageController::class, 'index'])->name('index');
    //     Route::get('/{chatJid}', [App\Http\Controllers\MessageController::class, 'show'])->name('show');
    //     Route::post('/{chatJid}/fetch-api', [App\Http\Controllers\MessageController::class, 'fetchFromApi'])->name('fetch-api');
    // });

    // Groups routes
    Route::resource('groups', App\Http\Controllers\GroupController::class)->middleware('subscription.security');
    Route::get('/groups/{group}/start-mass-sending', [App\Http\Controllers\GroupController::class, 'startMassSending'])->name('groups.start-mass-sending')->middleware('subscription.security');

    // Plans routes
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/api/plans', [PlanController::class, 'getPlans'])->name('api.plans.list');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
    Route::post('/plans/{plan}/checkout', [PlanController::class, 'checkout'])->name('plans.checkout');
    Route::get('/plans/{plan}/checkout-page', [PlanController::class, 'checkoutPage'])->name('plans.checkout-page');
    
    // Plans admin routes (only for admins)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/admin/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/plans', [PlanController::class, 'admin'])->name('plans.admin');
        Route::get('/api/admin/plans', [PlanController::class, 'getAdminPlans'])->name('api.admin.plans.list');
        Route::get('/admin/plans/create', [PlanController::class, 'create'])->name('plans.create');
        Route::post('/admin/plans', [PlanController::class, 'store'])->name('plans.store');
        Route::get('/admin/plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
        Route::put('/admin/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/admin/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
        
        // Subscriptions admin routes
        Route::get('/admin/subscriptions', [App\Http\Controllers\AdminSubscriptionController::class, 'index'])->name('admin.subscriptions.index');
        Route::get('/admin/subscriptions/{subscription}', [App\Http\Controllers\AdminSubscriptionController::class, 'show'])->name('admin.subscriptions.show');
        Route::get('/admin/subscriptions/{subscription}/edit', [App\Http\Controllers\AdminSubscriptionController::class, 'edit'])->name('admin.subscriptions.edit');
        Route::put('/admin/subscriptions/{subscription}', [App\Http\Controllers\AdminSubscriptionController::class, 'update'])->name('admin.subscriptions.update');
        Route::post('/admin/subscriptions/{subscription}/cancel', [App\Http\Controllers\AdminSubscriptionController::class, 'cancel'])->name('admin.subscriptions.cancel');
        Route::post('/admin/subscriptions/{subscription}/reactivate', [App\Http\Controllers\AdminSubscriptionController::class, 'reactivate'])->name('admin.subscriptions.reactivate');
        Route::post('/admin/subscriptions/{subscription}/sync', [App\Http\Controllers\AdminSubscriptionController::class, 'sync'])->name('admin.subscriptions.sync');
        Route::delete('/admin/subscriptions/{subscription}', [App\Http\Controllers\AdminSubscriptionController::class, 'destroy'])->name('admin.subscriptions.destroy');
        
        // Campaigns admin routes
        Route::get('/admin/campaigns', [App\Http\Controllers\AdminCampaignController::class, 'index'])->name('admin.campaigns.index');
        Route::get('/admin/campaigns/statistics', [App\Http\Controllers\AdminCampaignController::class, 'statistics'])->name('admin.campaigns.statistics');
        Route::get('/admin/campaigns/{campaign}', [App\Http\Controllers\AdminCampaignController::class, 'show'])->name('admin.campaigns.show');
        Route::get('/admin/campaigns/{campaign}/edit', [App\Http\Controllers\AdminCampaignController::class, 'edit'])->name('admin.campaigns.edit');
        Route::put('/admin/campaigns/{campaign}', [App\Http\Controllers\AdminCampaignController::class, 'update'])->name('admin.campaigns.update');
        Route::post('/admin/campaigns/{campaign}/cancel', [App\Http\Controllers\AdminCampaignController::class, 'cancel'])->name('admin.campaigns.cancel');
        Route::post('/admin/campaigns/{campaign}/restart', [App\Http\Controllers\AdminCampaignController::class, 'restart'])->name('admin.campaigns.restart');
        Route::delete('/admin/campaigns/{campaign}', [App\Http\Controllers\AdminCampaignController::class, 'destroy'])->name('admin.campaigns.destroy');
        
        // Analytics admin routes
        Route::get('/admin/analytics', [App\Http\Controllers\AdminAnalyticsController::class, 'index'])->name('admin.analytics.index');
        Route::post('/admin/analytics/export', [App\Http\Controllers\AdminAnalyticsController::class, 'export'])->name('admin.analytics.export');
        
        // Settings admin routes
        Route::get('/admin/settings', [App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings.index');
        Route::post('/admin/settings/general', [App\Http\Controllers\AdminSettingsController::class, 'updateGeneral'])->name('admin.settings.update-general');
        Route::post('/admin/settings/whatsapp', [App\Http\Controllers\AdminSettingsController::class, 'updateWhatsApp'])->name('admin.settings.update-whatsapp');
        Route::post('/admin/settings/stripe', [App\Http\Controllers\AdminSettingsController::class, 'updateStripe'])->name('admin.settings.update-stripe');
        Route::post('/admin/settings/email', [App\Http\Controllers\AdminSettingsController::class, 'updateEmail'])->name('admin.settings.update-email');
        Route::post('/admin/settings/notifications', [App\Http\Controllers\AdminSettingsController::class, 'updateNotifications'])->name('admin.settings.update-notifications');
        Route::post('/admin/settings/test-email', [App\Http\Controllers\AdminSettingsController::class, 'testEmail'])->name('admin.settings.test-email');
        Route::post('/admin/settings/clear-cache', [App\Http\Controllers\AdminSettingsController::class, 'clearCache'])->name('admin.settings.clear-cache');
        
        // Logs admin routes
        Route::get('/admin/logs', [App\Http\Controllers\AdminLogsController::class, 'index'])->name('admin.logs.index');
        Route::get('/admin/logs/system', [App\Http\Controllers\AdminLogsController::class, 'system'])->name('admin.logs.system');
        Route::get('/admin/logs/activity', [App\Http\Controllers\AdminLogsController::class, 'activity'])->name('admin.logs.activity');
        Route::get('/admin/logs/errors', [App\Http\Controllers\AdminLogsController::class, 'errors'])->name('admin.logs.errors');
        Route::get('/admin/logs/show', [App\Http\Controllers\AdminLogsController::class, 'show'])->name('admin.logs.show');
        Route::get('/admin/logs/download', [App\Http\Controllers\AdminLogsController::class, 'download'])->name('admin.logs.download');
        Route::post('/admin/logs/clear', [App\Http\Controllers\AdminLogsController::class, 'clear'])->name('admin.logs.clear');
        
        // Notifications admin routes
        Route::get('/admin/notifications', [App\Http\Controllers\AdminNotificationsController::class, 'index'])->name('admin.notifications.index');
        Route::get('/admin/notifications/create', [App\Http\Controllers\AdminNotificationsController::class, 'create'])->name('admin.notifications.create');
        Route::post('/admin/notifications', [App\Http\Controllers\AdminNotificationsController::class, 'store'])->name('admin.notifications.store');
        Route::get('/admin/notifications/{notification}', [App\Http\Controllers\AdminNotificationsController::class, 'show'])->name('admin.notifications.show');
        Route::get('/admin/notifications/{notification}/edit', [App\Http\Controllers\AdminNotificationsController::class, 'edit'])->name('admin.notifications.edit');
        Route::put('/admin/notifications/{notification}', [App\Http\Controllers\AdminNotificationsController::class, 'update'])->name('admin.notifications.update');
        Route::post('/admin/notifications/{notification}/send', [App\Http\Controllers\AdminNotificationsController::class, 'send'])->name('admin.notifications.send');
        Route::post('/admin/notifications/{notification}/cancel', [App\Http\Controllers\AdminNotificationsController::class, 'cancel'])->name('admin.notifications.cancel');
        Route::delete('/admin/notifications/{notification}', [App\Http\Controllers\AdminNotificationsController::class, 'destroy'])->name('admin.notifications.destroy');
        Route::post('/admin/notifications/bulk-send', [App\Http\Controllers\AdminNotificationsController::class, 'bulkSend'])->name('admin.notifications.bulk-send');
    });

    // Subscriptions routes (specific routes first to avoid conflicts)
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/success', [SubscriptionController::class, 'success'])->name('subscriptions.success');
    Route::get('/subscriptions/error', [SubscriptionController::class, 'error'])->name('subscriptions.error');
    Route::get('/subscriptions/status/check', [SubscriptionController::class, 'checkStatus'])->name('subscriptions.status-check');
    // Dynamic routes last
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('rate.limit:5,15');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('rate.limit:3,60');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Bestfy webhook (no authentication required, but with security middleware)
Route::post('/bestfy/webhook', [SubscriptionController::class, 'webhook'])
    ->middleware(['bestfy.webhook'])
    ->name('bestfy.webhook');

// Admin API routes (token-based authentication)
Route::prefix('admin')->middleware(['token.auth', 'rate.limit:100,60'])->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/{id}/regenerate-token', [AdminUserController::class, 'regenerateToken'])->name('admin.users.regenerate-token');
    
    // Wuzapi users list
    Route::get('/wuzapi-users', [AdminUserController::class, 'listWuzapiUsers'])->name('admin.wuzapi-users');
});
