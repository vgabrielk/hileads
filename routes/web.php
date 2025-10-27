<?php

use Illuminate\Support\Facades\Route;
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
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    // WhatsApp Connections
    Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index')->middleware('subscription.security');
    Route::get('/whatsapp/connect', [WhatsAppController::class, 'connect'])->name('whatsapp.connect')->middleware('subscription.security');
    Route::get('/whatsapp/connect-flow', [WhatsAppController::class, 'showConnectFlow'])->name('whatsapp.connect-flow')->middleware('subscription.security');
    Route::get('/whatsapp/status', [WhatsAppController::class, 'checkStatus'])->name('whatsapp.status')->middleware('subscription.security');
    Route::post('/whatsapp/disconnect', [WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect')->middleware('subscription.security');
    Route::post('/whatsapp/logout', [WhatsAppController::class, 'logout'])->name('whatsapp.logout')->middleware('subscription.security');
    Route::get('/whatsapp/{whatsapp}', [WhatsAppController::class, 'show'])->name('whatsapp.show')->middleware('subscription.security');
    Route::delete('/whatsapp/{whatsapp}', [WhatsAppController::class, 'destroy'])->name('whatsapp.destroy')->middleware('subscription.security');
    
    // WhatsApp API Endpoints for Connect Flow
    Route::post('/whatsapp/connect-session', [WhatsAppController::class, 'connectSession'])->name('whatsapp.connect-session')->middleware('subscription.security');
    Route::get('/whatsapp/get-qr', [WhatsAppController::class, 'getQR'])->name('whatsapp.get-qr')->middleware('subscription.security');
    Route::get('/whatsapp/check-status', [WhatsAppController::class, 'getStatus'])->name('whatsapp.check-status')->middleware('subscription.security');
    
    // Contacts (apenas visualização via API)
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    

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
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
    Route::post('/plans/{plan}/checkout', [PlanController::class, 'checkout'])->name('plans.checkout');
    Route::get('/plans/{plan}/checkout-page', [PlanController::class, 'checkoutPage'])->name('plans.checkout-page');

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
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Bestfy webhook (no authentication required, but with security middleware)
Route::post('/bestfy/webhook', [SubscriptionController::class, 'webhook'])
    ->middleware(['bestfy.webhook'])
    ->name('bestfy.webhook');

// Admin API routes (token-based authentication)
Route::prefix('admin')->middleware('token.auth')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/{id}/regenerate-token', [AdminUserController::class, 'regenerateToken'])->name('admin.users.regenerate-token');
    
    // Wuzapi users list
    Route::get('/wuzapi-users', [AdminUserController::class, 'listWuzapiUsers'])->name('admin.wuzapi-users');
});
