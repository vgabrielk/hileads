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
        $user = auth()->user();
        
        // Cache das estatísticas por 5 minutos
        $stats = Cache::remember("dashboard_stats_user_{$user->id}", 300, function () use ($user) {
            $contactsCount = 0;
            $groupsCount = 0;
            
            // Buscar dados da API Wuzapi (mesma fonte da página /contacts)
            try {
                $service = new WuzapiService($user->api_token);
                
                // Buscar grupos da API
                $groupsResponse = $service->getGroups();
                if ($groupsResponse['success'] ?? false) {
                    $groupsCount = count($groupsResponse['data'] ?? []);
                    \Log::info('Dashboard: Grupos encontrados na API', ['count' => $groupsCount, 'user_id' => $user->id]);
                } else {
                    \Log::warning('Dashboard: Falha ao buscar grupos da API', ['user_id' => $user->id, 'response' => $groupsResponse]);
                }
                
                // Buscar contactos da API
                $contactsResponse = $service->getContacts();
                if ($contactsResponse['success'] ?? false) {
                    $contactsCount = count($contactsResponse['data'] ?? []);
                }
            } catch (\Exception $e) {
                \Log::warning('Erro ao buscar dados da API na dashboard: ' . $e->getMessage());
                // Fallback para banco de dados local se a API falhar
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

        // Cache do status de acesso por 2 minutos
        $accessStatus = Cache::remember("access_status_user_{$user->id}", 120, function () use ($user) {
            $currentSubscription = $user->activeSubscription()->with('plan')->first();
            return [
                'is_admin' => $user->isAdmin(),
                'has_subscription' => $user->hasActiveSubscription(),
                'has_access' => $user->hasFeatureAccess(),
                'current_plan' => $currentSubscription ? $currentSubscription->plan : null,
            ];
        });

        // Buscar conexões do banco (mesma fonte da página /whatsapp)
        $recentConnections = Cache::remember("whatsapp_connections_user_{$user->id}", 120, function () use ($user) {
            return $user->whatsappConnections()
                ->select(['id', 'phone_number', 'status', 'created_at', 'last_sync', 'instance_id'])
                ->latest()
                ->take(5)
                ->get();
        });

        // Verificar se há conexões ativas no banco
        $activeConnections = $recentConnections->where('status', 'active');
        
        // Status da ligação (mesma lógica da página /whatsapp)
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
                'message' => 'Nenhuma ligação ativa. Clique em "Ligar WhatsApp" para iniciar.'
            ];
        }

        // Buscar grupos recentes da API Wuzapi
        $recentGroups = collect([]);
        try {
            $groupsResponse = $this->service()->getGroups();
            if ($groupsResponse['success'] ?? false) {
                $recentGroups = collect($groupsResponse['data'] ?? [])->map(function($group) {
                    return (object)[
                        'group_name' => $group['Name'] ?? 'Grupo sem nome',
                        'participants_count' => count($group['Participants'] ?? []),
                        'is_extracted' => true, // Grupos da API estão sempre extraídos
                        'created_at' => $group['GroupCreated'] ?? null,
                    ];
                })->take(5);
            }
        } catch (\Exception $e) {
            \Log::warning('Erro ao buscar grupos recentes da API: ' . $e->getMessage());
            // Fallback para banco de dados local
            $recentGroups = $user->whatsappGroups()
                ->select(['id', 'group_name', 'participants_count', 'created_at', 'whatsapp_connection_id'])
                ->with(['whatsappConnection:id,phone_number,status'])
                ->latest()
                ->take(5)
                ->get();
        }

        $recentContacts = $user->extractedContacts()
            ->select(['id', 'contact_name', 'phone_number', 'created_at', 'whatsapp_group_id'])
            ->with(['whatsappGroup:id,group_name'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recentConnections', 'recentGroups', 'recentContacts', 'accessStatus', 'whatsappStatus'));
    }
}
