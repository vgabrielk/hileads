<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsAppConnection;
use App\Models\WhatsAppGroup;
use App\Models\ExtractedContact;
use App\Models\MassSending;
use App\Services\WuzapiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function service(): WuzapiService
    {
        $token = auth()->user()->api_token;
        return new WuzapiService($token);
    }

    public function index()
    {
        return view('dashboard');
    }

    // API Endpoints para carregamento assíncrono
    public function getStats()
    {
        $user = auth()->user();
        
        $stats = Cache::remember("dashboard_stats_user_{$user->id}", 300, function () use ($user) {
            $contactsCount = 0;
            $groupsCount = 0;
            
            try {
                $service = new WuzapiService($user->api_token);
                
                $groupsResponse = $service->getGroups();
                if ($groupsResponse['success'] ?? false) {
                    $groupsCount = count($groupsResponse['data'] ?? []);
                }
                
                $contactsResponse = $service->getContacts();
                if ($contactsResponse['success'] ?? false) {
                    $contactsCount = count($contactsResponse['data'] ?? []);
                }
            } catch (\Exception $e) {
                \Log::warning('Erro ao buscar dados da API na dashboard: ' . $e->getMessage());
                $groupsCount = $user->whatsappGroups()->count();
                $contactsCount = $user->extractedContacts()->count();
            }
            
            return [
                'connections' => $user->whatsappConnections()->count(),
                'groups' => $groupsCount,
                'contacts' => $contactsCount,
                'mass-sendings' => $user->massSendings()->count(),
            ];
        });

        $html = view('dashboard.partials.stats-cards', compact('stats'))->render();
        return response()->json(['html' => $html, 'data' => $stats]);
    }

    public function getAccessStatus()
    {
        $user = auth()->user();
        
        $accessStatus = Cache::remember("access_status_user_{$user->id}", 120, function () use ($user) {
            $currentSubscription = $user->activeSubscription()->with('plan')->first();
            return [
                'is_admin' => $user->isAdmin(),
                'has_subscription' => $user->hasActiveSubscription(),
                'has_access' => $user->hasFeatureAccess(),
                'current_plan' => $currentSubscription ? $currentSubscription->plan : null,
            ];
        });

        $html = view('dashboard.partials.access-status', compact('accessStatus'))->render();
        return response()->json(['html' => $html, 'data' => $accessStatus]);
    }

    public function getRecentConnections()
    {
        $user = auth()->user();
        
        $recentConnections = Cache::remember("whatsapp_connections_user_{$user->id}", 120, function () use ($user) {
            return $user->whatsappConnections()
                ->select(['id', 'phone_number', 'status', 'created_at', 'last_sync', 'instance_id'])
                ->latest()
                ->take(5)
                ->get();
        });

        $activeConnections = $recentConnections->where('status', 'active');
        
        $whatsappStatus = null;
        if ($activeConnections->count() > 0) {
            $whatsappStatus = [
                'success' => true,
                'message' => 'Conexões WhatsApp encontradas.',
                'data' => [
                    'Connected' => true,
                    'LoggedIn' => true
                ]
            ];
        } else {
            $whatsappStatus = [
                'success' => false,
                'message' => 'Nenhuma conexão ativa. Clique em "Ligar WhatsApp" para iniciar.'
            ];
        }

        $html = view('dashboard.partials.recent-connections', compact('recentConnections', 'whatsappStatus'))->render();
        return response()->json(['html' => $html, 'data' => $recentConnections]);
    }

    public function getRecentGroups()
    {
        $user = auth()->user();
        
        $recentGroups = collect([]);
        try {
            $groupsResponse = $this->service()->getGroups();
            if ($groupsResponse['success'] ?? false) {
                $recentGroups = collect($groupsResponse['data'] ?? [])->map(function($group) {
                    return (object)[
                        'group_name' => $group['Name'] ?? 'Grupo sem nome',
                        'participants_count' => count($group['Participants'] ?? []),
                        'is_extracted' => true,
                        'created_at' => $group['GroupCreated'] ?? null,
                    ];
                })->take(5);
            }
        } catch (\Exception $e) {
            \Log::warning('Erro ao buscar grupos recentes da API: ' . $e->getMessage());
            $recentGroups = $user->whatsappGroups()
                ->select(['id', 'group_name', 'participants_count', 'created_at', 'whatsapp_connection_id'])
                ->with(['whatsappConnection:id,phone_number,status'])
                ->latest()
                ->take(5)
                ->get();
        }

        $html = view('dashboard.partials.recent-groups', compact('recentGroups'))->render();
        return response()->json(['html' => $html, 'data' => $recentGroups]);
    }

    public function getRecentContacts()
    {
        $user = auth()->user();
        
        $recentContacts = $user->extractedContacts()
            ->select(['id', 'contact_name', 'phone_number', 'created_at', 'whatsapp_group_id', 'status'])
            ->with(['whatsappGroup:id,group_name'])
            ->latest()
            ->take(10)
            ->get();

        $html = view('dashboard.partials.recent-contacts', compact('recentContacts'))->render();
        return response()->json(['html' => $html, 'data' => $recentContacts]);
    }
}
