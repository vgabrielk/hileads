<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WuzapiService;

class ContactController extends Controller
{
    private function service(): WuzapiService
    {
        $token = auth()->user()->api_token;
        return new WuzapiService($token);
    }

    public function index(Request $request)
    {
        // Get groups from Wuzapi API in real-time
        $groups = [];
        $contacts = [];
        $stats = [
            'total' => 0,
            'groups' => 0,
        ];
        
        // Pagination parameters
        $perPage = 50; // Limite de contatos por página
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        
        try {
            $groupsResponse = $this->service()->getGroups();
            if ($groupsResponse['success'] ?? false) {
                $groups = collect($groupsResponse['data'] ?? [])->map(function($group) {
                    return [
                        'jid' => $group['JID'] ?? '',
                        'name' => $group['Name'] ?? 'Grupo sem nome',
                        'participants_count' => count($group['Participants'] ?? []),
                        'participants' => $group['Participants'] ?? [],
                        'is_announce' => $group['IsAnnounce'] ?? false,
                        'created_at' => $group['GroupCreated'] ?? null,
                        'photo' => $this->generateGroupAvatar($group['Name'] ?? 'Grupo'),
                    ];
                });
                
                $stats['groups'] = $groups->count();
                $apiError = false;
            } 
            else {
                // API não disponível - apenas grupos vazios
                \Log::warning('Failed to get groups from API: ' . ($groupsResponse['message'] ?? 'Unknown error'));
                $groups = collect([]);
                $stats['groups'] = 0;
                $apiError = true;
                
                /* BACKUP LOCAL - COMENTADO
                // Se não conseguir procurar grupos da API, tenta usar o banco de dados local
                \Log::warning('Failed to get groups from API, trying local database: ' . ($groupsResponse['message'] ?? 'Unknown error'));
                
                // Procurar grupos do banco de dados local
                $localGroups = \App\Models\WhatsAppGroup::with('contacts')->get();
                $groups = $localGroups->map(function($group) {
                    // Gerar avatar do grupo
                    $groupPhoto = $this->generateGroupAvatar($group->name ?? 'Grupo');
                    
                    return [
                        'jid' => $group->jid ?? '',
                        'name' => $group->name ?? 'Grupo sem nome',
                        'participants_count' => $group->contacts->count(),
                        'participants' => $group->contacts->pluck('phone')->map(fn($p) => $p . '@s.whatsapp.net')->toArray(),
                        'is_announce' => false,
                        'created_at' => $group->created_at,
                        'photo' => $groupPhoto,
                        'from_database' => true,
                    ];
                });
                
                $stats['groups'] = $groups->count();
                $apiError = true;
                */
            }
            
            // Get contacts from Wuzapi API
            $contactsResponse = $this->service()->getContacts();
            if ($contactsResponse['success'] ?? false) {
                $contactsData = $contactsResponse['data'] ?? [];
                $allContacts = collect($contactsData)->map(function($contact, $jid) use ($groups) {
                    $phone = explode('@', $jid)[0] ?? '';
                    
                    // Find which group this contact belongs to
                    $groupInfo = collect($groups)->first(function($group) use ($jid) {
                        return in_array($jid, $group['participants'] ?? []);
                    });
                    
                    return [
                        'jid' => $jid,
                        'phone' => $phone,
                        'name' => $contact['PushName'] ?? null,
                        'user_name' => $contact['PushName'] ?? $phone,
                        'found' => $contact['Found'] ?? false,
                        'group_name' => $groupInfo['name'] ?? 'Grupo não encontrado',
                        'group_jid' => $groupInfo['jid'] ?? '',
                        'avatar' => null, // Desabilitado temporariamente para evitar timeout
                    ];
                })->values();
                
                // Aplicar busca simples
                if (!empty($search)) {
                    $allContacts = $allContacts->filter(function($contact) use ($search) {
                        return stripos($contact['name'] ?? '', $search) !== false ||
                               stripos($contact['phone'] ?? '', $search) !== false ||
                               stripos($contact['group_name'] ?? '', $search) !== false;
                    });
                }
                
                $stats['total'] = $allContacts->count();
                
                // Aplicar paginação
                $contacts = $allContacts->forPage($page, $perPage)->values();
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao procurar dados da Wuzapi: ' . $e->getMessage());
            
            // API não disponível - retornar dados vazios
            $groups = collect([]);
            $contacts = [];
            $stats = [
                'total' => 0,
                'groups' => 0,
            ];
            $apiError = true;
            
            /* BACKUP LOCAL - COMENTADO
            // Tentar procurar grupos do banco de dados local como fallback
            try {
                $localGroups = \App\Models\WhatsAppGroup::with('contacts')->get();
                $groups = $localGroups->map(function($group) {
                    // Gerar avatar do grupo
                    $groupPhoto = $this->generateGroupAvatar($group->name ?? 'Grupo');
                    
                    return [
                        'jid' => $group->jid ?? '',
                        'name' => $group->name ?? 'Grupo sem nome',
                        'participants_count' => $group->contacts->count(),
                        'participants' => $group->contacts->pluck('phone')->map(fn($p) => $p . '@s.whatsapp.net')->toArray(),
                        'is_announce' => false,
                        'created_at' => $group->created_at,
                        'photo' => $groupPhoto,
                        'from_database' => true,
                    ];
                });
                
                $stats['groups'] = $groups->count();
                $apiError = true;
            } catch (\Exception $e2) {
                \Log::error('Erro ao procurar grupos do banco de dados: ' . $e2->getMessage());
                $groups = [];
                $apiError = true;
            }
            
            // Se não conseguiu procurar grupos, tenta procurar contatos mesmo assim
            $contacts = [];
            try {
                $contactsResponse = $this->service()->getContacts();
                if ($contactsResponse['success'] ?? false) {
                    $contactsData = $contactsResponse['data'] ?? [];
                    $allContacts = collect($contactsData)->map(function($contact, $jid) use ($groups) {
                        $phone = explode('@', $jid)[0] ?? '';
                        
                        // Find which group this contact belongs to
                        $groupInfo = collect($groups)->first(function($group) use ($jid) {
                            return in_array($jid, $group['participants'] ?? []);
                        });
                        
                        return [
                            'jid' => $jid,
                            'phone' => $phone,
                            'name' => $contact['PushName'] ?? null,
                            'user_name' => $contact['PushName'] ?? $phone,
                            'found' => $contact['Found'] ?? false,
                            'group_name' => $groupInfo['name'] ?? 'Grupo não encontrado',
                            'group_jid' => $groupInfo['jid'] ?? '',
                            'avatar' => null,
                        ];
                    })->values();
                    
                    // Aplicar busca simples
                    if (!empty($search)) {
                        $allContacts = $allContacts->filter(function($contact) use ($search) {
                            return stripos($contact['name'] ?? '', $search) !== false ||
                                   stripos($contact['phone'] ?? '', $search) !== false ||
                                   stripos($contact['group_name'] ?? '', $search) !== false;
                        });
                    }
                    
                    $stats['total'] = $allContacts->count();
                    
                    // Aplicar paginação
                    $contacts = $allContacts->forPage($page, $perPage)->values()->toArray();
                }
            } catch (\Exception $e3) {
                \Log::error('Erro ao procurar contatos: ' . $e3->getMessage());
            }
            */
            
            return view('contacts.index', [
                'groups' => $groups,
                'contacts' => $contacts,
                'stats' => $stats,
                'apiError' => $apiError,
                'perPage' => $perPage,
                'totalPages' => ceil($stats['total'] / $perPage),
                'currentPage' => $page,
                'search' => $search,
                'totalContacts' => $stats['total'],
                'error' => 'Erro ao ligar com a API. Verifique a sua ligação com o WhatsApp.'
            ]);
        }
        
        // Calcular informações de paginação
        $totalContacts = $stats['total'];
        $totalPages = ceil($totalContacts / $perPage);
        $currentPage = $page;
        
        return view('contacts.index', compact('groups', 'contacts', 'stats', 'apiError', 'perPage', 'totalPages', 'currentPage', 'search', 'totalContacts'));
    }

    /**
     * Gera um avatar baseado no nome do grupo
     */
    private function generateGroupAvatar(string $groupName): string
    {
        // Cores disponíveis para os avatares
        $colors = [
            'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500',
            'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500',
            'bg-orange-500', 'bg-cyan-500', 'bg-lime-500', 'bg-amber-500'
        ];
        
        // Pega as primeiras letras do nome do grupo
        $initials = '';
        $words = explode(' ', trim($groupName));
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        
        // Se não conseguir pegar iniciais, usa as primeiras letras do nome
        if (empty($initials)) {
            $initials = strtoupper(substr($groupName, 0, 2));
        }
        
        // Seleciona uma cor baseada no hash do nome
        $colorIndex = crc32($groupName) % count($colors);
        $color = $colors[$colorIndex];
        
        return "data:image/svg+xml;base64," . base64_encode("
            <svg width='56' height='56' viewBox='0 0 56 56' xmlns='http://www.w3.org/2000/svg'>
                <rect width='56' height='56' rx='12' fill='currentColor' class='{$color}'/>
                <text x='28' y='32' text-anchor='middle' fill='white' font-family='system-ui, sans-serif' font-size='16' font-weight='bold'>{$initials}</text>
            </svg>
        ");
    }

    /**
     * Gera um avatar baseado no nome do utilizador
     */
    private function generateUserAvatar(string $userName): string
    {
        // Cores disponíveis para os avatares
        $colors = [
            'bg-purple-500', 'bg-blue-500', 'bg-green-500', 'bg-orange-500',
            'bg-red-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500',
            'bg-yellow-500', 'bg-cyan-500', 'bg-lime-500', 'bg-amber-500'
        ];
        
        // Pega as primeiras letras do nome do utilizador
        $initials = '';
        $words = explode(' ', trim($userName));
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        
        // Se não conseguir pegar iniciais, usa as primeiras letras do nome
        if (empty($initials)) {
            $initials = strtoupper(substr($userName, 0, 2));
        }
        
        // Seleciona uma cor baseada no hash do nome
        $colorIndex = crc32($userName) % count($colors);
        $color = $colors[$colorIndex];
        
        return "data:image/svg+xml;base64," . base64_encode("
            <svg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'>
                <rect width='40' height='40' rx='12' fill='currentColor' class='{$color}'/>
                <text x='20' y='24' text-anchor='middle' fill='white' font-family='system-ui, sans-serif' font-size='12' font-weight='bold'>{$initials}</text>
            </svg>
        ");
    }

    /**
     * Tenta procurar o avatar real do utilizador, se falhar gera um avatar
     */
    private function getUserAvatarOrGenerate(string $userName, string $phone): string
    {
        try {
            $avatarResponse = $this->service()->getUserAvatar($phone);
            if ($avatarResponse['success'] ?? false) {
                $avatarUrl = $avatarResponse['data']['data']['url'] ?? null;
                if ($avatarUrl) {
                    return $avatarUrl;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Could not get user avatar for ' . $phone . ': ' . $e->getMessage());
        }
        
        // Se não conseguir procurar o avatar real, gera um
        return $this->generateUserAvatar($userName);
    }
}
