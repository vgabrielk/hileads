<?php

namespace App\Http\Controllers;

use App\Models\MassSending;
use App\Models\User;
use App\Models\WhatsAppConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminCampaignController extends Controller
{
    /**
     * Display a listing of all campaigns for admin.
     */
    public function index(Request $request)
    {
        $query = MassSending::with(['user', 'whatsappConnection']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('user_search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_search . '%')
                  ->orWhere('email', 'like', '%' . $request->user_search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('message_type')) {
            $query->where('message_type', $request->message_type);
        }

        // Use a more efficient query with proper indexing
        $campaigns = $query->select([
            'id', 'user_id', 'name', 'message', 'message_type', 'status', 
            'total_recipients', 'sent_count', 'failed_count', 'created_at'
        ])
        ->orderBy('id', 'desc') // Use ID instead of created_at for better performance
        ->paginate(20);
        $users = User::where('role', 'user')->orderBy('name')->get();
        $statuses = ['pending', 'sending', 'completed', 'failed', 'cancelled'];
        $messageTypes = ['text', 'image', 'video', 'audio', 'document'];

        // Estatísticas - use queries mais eficientes
        $stats = [
            'total' => MassSending::count(),
            'pending' => MassSending::where('status', 'pending')->count(),
            'sending' => MassSending::where('status', 'sending')->count(),
            'completed' => MassSending::where('status', 'completed')->count(),
            'failed' => MassSending::where('status', 'failed')->count(),
            'cancelled' => MassSending::where('status', 'cancelled')->count(),
            'total_recipients' => MassSending::sum('total_recipients') ?: 0,
            'sent_messages' => MassSending::sum('sent_count') ?: 0,
        ];

        return view('admin.campaigns.index', compact(
            'campaigns', 
            'users', 
            'statuses', 
            'messageTypes',
            'stats'
        ));
    }

    /**
     * Display the specified campaign.
     */
    public function show(MassSending $campaign)
    {
        $campaign->load(['user', 'whatsappConnection']);
        
        return view('admin.campaigns.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(MassSending $campaign)
    {
        $campaign->load(['user', 'whatsappConnection']);
        $statuses = ['pending', 'sending', 'completed', 'failed', 'cancelled'];

        return view('admin.campaigns.edit', compact('campaign', 'statuses'));
    }

    /**
     * Update the specified campaign.
     */
    public function update(Request $request, MassSending $campaign)
    {
        $request->validate([
            'status' => 'required|in:pending,sending,completed,failed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $oldStatus = $campaign->status;

            $campaign->update([
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            Log::info('Admin updated campaign', [
                'campaign_id' => $campaign->id,
                'user_id' => $campaign->user_id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'admin_id' => auth()->id()
            ]);

            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('success', 'Campanha atualizada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to update campaign', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao atualizar campanha: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancel a campaign.
     */
    public function cancel(MassSending $campaign)
    {
        try {
            $campaign->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            Log::info('Admin cancelled campaign', [
                'campaign_id' => $campaign->id,
                'user_id' => $campaign->user_id,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', 'Campanha cancelada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to cancel campaign', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao cancelar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Restart a failed campaign.
     */
    public function restart(MassSending $campaign)
    {
        try {
            $campaign->update([
                'status' => 'pending',
                'cancelled_at' => null,
                'failed_at' => null,
                'sent_count' => 0,
                'failed_count' => 0,
            ]);

            Log::info('Admin restarted campaign', [
                'campaign_id' => $campaign->id,
                'user_id' => $campaign->user_id,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', 'Campanha reiniciada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to restart campaign', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao reiniciar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Delete a campaign.
     */
    public function destroy(MassSending $campaign)
    {
        try {
            $campaignId = $campaign->id;
            $userId = $campaign->user_id;
            
            $campaign->delete();

            Log::info('Admin deleted campaign', [
                'campaign_id' => $campaignId,
                'user_id' => $userId,
                'admin_id' => auth()->id()
            ]);

            return redirect()->route('admin.campaigns.index')
                ->with('success', 'Campanha eliminada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Failed to delete campaign', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao eliminar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Get campaign statistics.
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);

        $stats = [
            'total_campaigns' => MassSending::where('created_at', '>=', $startDate)->count(),
            'completed_campaigns' => MassSending::where('created_at', '>=', $startDate)
                ->where('status', 'completed')->count(),
            'failed_campaigns' => MassSending::where('created_at', '>=', $startDate)
                ->where('status', 'failed')->count(),
            'total_recipients' => MassSending::where('created_at', '>=', $startDate)
                ->sum('total_recipients'),
            'sent_messages' => MassSending::where('created_at', '>=', $startDate)
                ->sum('sent_count'),
            'success_rate' => 0,
        ];

        if ($stats['total_campaigns'] > 0) {
            $stats['success_rate'] = round(($stats['completed_campaigns'] / $stats['total_campaigns']) * 100, 2);
        }

        // Campaigns by day
        $campaignsByDay = MassSending::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Campaigns by status
        $campaignsByStatus = MassSending::where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Top users by campaigns
        $topUsers = MassSending::where('created_at', '>=', $startDate)
            ->with('user')
            ->selectRaw('user_id, COUNT(*) as campaign_count, SUM(total_recipients) as total_recipients')
            ->groupBy('user_id')
            ->orderBy('campaign_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.campaigns.statistics', compact(
            'stats',
            'campaignsByDay',
            'campaignsByStatus',
            'topUsers',
            'period'
        ));
    }
}