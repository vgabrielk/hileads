<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AdminNotificationsController extends Controller
{
    /**
     * Display the notifications page.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $status = $request->get('status', 'all');
        $perPage = $request->get('per_page', 20);

        $query = Notification::with('user');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $users = User::where('is_active', true)->orderBy('name')->get();
        $types = ['info', 'success', 'warning', 'error', 'system'];
        $statuses = ['pending', 'sent', 'failed', 'read'];

        return view('admin.notifications.index', compact(
            'notifications',
            'users',
            'types',
            'statuses',
            'type',
            'status',
            'perPage'
        ));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $types = ['info', 'success', 'warning', 'error', 'system'];
        $channels = ['database', 'email', 'push'];

        return view('admin.notifications.create', compact('users', 'types', 'channels'));
    }

    /**
     * Store a newly created notification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,error,system',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:database,email,push',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'send_immediately' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        try {
            DB::beginTransaction();

            $userIds = $request->user_ids ?? User::where('is_active', true)->pluck('id')->toArray();
            $scheduledAt = $request->send_immediately ? now() : $request->scheduled_at;

            foreach ($userIds as $userId) {
                $notification = Notification::create([
                    'user_id' => $userId,
                    'title' => $request->title,
                    'message' => $request->message,
                    'type' => $request->type,
                    'channels' => $request->channels,
                    'status' => $request->send_immediately ? 'pending' : 'scheduled',
                    'scheduled_at' => $scheduledAt,
                    'sent_at' => null,
                    'read_at' => null,
                ]);

                // Send immediately if requested
                if ($request->send_immediately) {
                    $this->sendNotification($notification);
                }
            }

            DB::commit();

            Log::info('Notifications created by admin', [
                'admin_id' => auth()->id(),
                'title' => $request->title,
                'type' => $request->type,
                'user_count' => count($userIds),
                'channels' => $request->channels,
                'send_immediately' => $request->send_immediately
            ]);

            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notificações criadas com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create notifications', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao criar notificações: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified notification.
     */
    public function show(Notification $notification)
    {
        $notification->load('user');
        
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified notification.
     */
    public function edit(Notification $notification)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $types = ['info', 'success', 'warning', 'error', 'system'];
        $channels = ['database', 'email', 'push'];

        return view('admin.notifications.edit', compact('notification', 'users', 'types', 'channels'));
    }

    /**
     * Update the specified notification.
     */
    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,error,system',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:database,email,push',
            'status' => 'required|in:pending,sent,failed,scheduled',
            'scheduled_at' => 'nullable|date',
        ]);

        try {
            $notification->update([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'channels' => $request->channels,
                'status' => $request->status,
                'scheduled_at' => $request->scheduled_at,
            ]);

            Log::info('Notification updated by admin', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id,
                'title' => $request->title,
                'type' => $request->type,
                'status' => $request->status
            ]);

            return redirect()->route('admin.notifications.show', $notification)
                ->with('success', 'Notificação atualizada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to update notification', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao atualizar notificação: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Send a notification.
     */
    public function send(Notification $notification)
    {
        try {
            $this->sendNotification($notification);

            Log::info('Notification sent by admin', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id
            ]);

            return redirect()->back()
                ->with('success', 'Notificação enviada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao enviar notificação: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a scheduled notification.
     */
    public function cancel(Notification $notification)
    {
        try {
            $notification->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            Log::info('Notification cancelled by admin', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id
            ]);

            return redirect()->back()
                ->with('success', 'Notificação cancelada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to cancel notification', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao cancelar notificação: ' . $e->getMessage());
        }
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        try {
            $notification->delete();

            Log::info('Notification deleted by admin', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id
            ]);

            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notificação eliminada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to delete notification', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao eliminar notificação: ' . $e->getMessage());
        }
    }

    /**
     * Send bulk notifications.
     */
    public function bulkSend(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array|min:1',
            'notification_ids.*' => 'exists:notifications,id',
        ]);

        try {
            $notifications = Notification::whereIn('id', $request->notification_ids)
                ->where('status', 'pending')
                ->get();

            $sentCount = 0;
            $failedCount = 0;

            foreach ($notifications as $notification) {
                try {
                    $this->sendNotification($notification);
                    $sentCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Failed to send notification in bulk', [
                        'notification_id' => $notification->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Bulk notifications sent by admin', [
                'admin_id' => auth()->id(),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount
            ]);

            $message = "Enviadas: {$sentCount}";
            if ($failedCount > 0) {
                $message .= ", Falharam: {$failedCount}";
            }

            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Failed to send bulk notifications', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao enviar notificações em lote: ' . $e->getMessage());
        }
    }

    /**
     * Send a notification through the specified channels.
     */
    private function sendNotification(Notification $notification)
    {
        $channels = $notification->channels ?? ['database'];
        $user = $notification->user;

        foreach ($channels as $channel) {
            try {
                switch ($channel) {
                    case 'database':
                        // Already stored in database
                        break;
                        
                    case 'email':
                        $this->sendEmailNotification($notification, $user);
                        break;
                        
                    case 'push':
                        $this->sendPushNotification($notification, $user);
                        break;
                }
            } catch (\Exception $e) {
                Log::error('Failed to send notification via channel', [
                    'notification_id' => $notification->id,
                    'channel' => $channel,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Send email notification.
     */
    private function sendEmailNotification(Notification $notification, User $user)
    {
        // This would send an actual email
        // For now, just log it
        Log::info('Email notification sent', [
            'notification_id' => $notification->id,
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    /**
     * Send push notification.
     */
    private function sendPushNotification(Notification $notification, User $user)
    {
        // This would send an actual push notification
        // For now, just log it
        Log::info('Push notification sent', [
            'notification_id' => $notification->id,
            'user_id' => $user->id
        ]);
    }
}