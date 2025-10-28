<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupContact;
use App\Services\WuzapiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    protected function service()
    {
        return new WuzapiService(auth()->user()->api_token);
    }

    public function index()
    {
        $user = Auth::user();
        $groups = $user->groups()->withCount('groupContacts')->latest()->get();
        
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Get contacts from Wuzapi API
        $apiContacts = [];
        $apiError = false;
        
        try {
            $response = $this->service()->getContacts();
            if ($response['success']) {
                $apiContacts = $response['data'];
            } else {
                $apiError = true;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching contacts for group creation: ' . $e->getMessage());
            $apiError = true;
        }

        // Load country codes from ddi.json
        $ddiPath = app_path('ddi.json');
        $countries = [];
        if (file_exists($ddiPath)) {
            $ddiData = json_decode(file_get_contents($ddiPath), true);
            // Sort by country name
            uasort($ddiData, function($a, $b) {
                return strcmp($a['pais'], $b['pais']);
            });
            $countries = $ddiData;
        }

        return view('groups.create', compact('apiContacts', 'apiError', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'contacts' => 'array',
            'contacts.*' => 'string',
            'manual_contacts' => 'array',
            'manual_contacts.*.phone' => 'required|string',
            'manual_contacts.*.name' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        // Create group
        $group = Group::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'contacts_count' => 0,
        ]);

        // Add contacts from API
        if ($request->contacts) {
            $this->addContactsFromApi($group, $request->contacts);
        }

        // Add manual contacts
        if ($request->manual_contacts) {
            $this->addManualContacts($group, $request->manual_contacts);
        }

        // Update contacts count
        $group->updateContactsCount();

        return redirect()->route('groups.index')->with('success', 'Grupo criado com sucesso!');
    }

    public function show(Group $group)
    {
        $this->authorize('view', $group);
        
        $contacts = $group->getContactsFromApi();
        
        return view('groups.show', compact('group', 'contacts'));
    }

    public function edit(Group $group)
    {
        $this->authorize('update', $group);
        
        $user = Auth::user();
        
        // Get contacts from Wuzapi API
        $apiContacts = [];
        $apiError = false;
        
        try {
            $response = $this->service()->getContacts();
            if ($response['success']) {
                $apiContacts = $response['data'];
            } else {
                $apiError = true;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching contacts for group edit: ' . $e->getMessage());
            $apiError = true;
        }

        // Get current group contacts
        $currentContacts = $group->groupContacts()->get();
        
        return view('groups.edit', compact('group', 'apiContacts', 'apiError', 'currentContacts'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorize('update', $group);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'contacts' => 'array',
            'contacts.*' => 'string',
            'manual_contacts' => 'array',
            'manual_contacts.*.phone' => 'required|string',
            'manual_contacts.*.name' => 'nullable|string',
        ]);

        // Update group
        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Clear existing contacts
        $group->groupContacts()->delete();

        // Add contacts from API
        if ($request->contacts) {
            $this->addContactsFromApi($group, $request->contacts);
        }

        // Add manual contacts
        if ($request->manual_contacts) {
            $this->addManualContacts($group, $request->manual_contacts);
        }

        // Update contacts count
        $group->updateContactsCount();

        return redirect()->route('groups.index')->with('success', 'Grupo atualizado com sucesso!');
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);
        
        $group->delete();
        
        return redirect()->route('groups.index')->with('success', 'Grupo eliminado com sucesso!');
    }

    public function startMassSending(Group $group)
    {
        $this->authorize('view', $group);
        
        // Store the original Group model
        $originalGroup = $group;
        
        $contacts = $group->getContactsFromApi();
        
        // Get WhatsApp groups in real-time from Wuzapi API (for compatibility with mass-sendings.create)
        $wuzapiGroups = [];
        $apiError = false;
        
        try {
            $response = $this->service()->getGroups();
            if ($response['success'] ?? false) {
                $wuzapiGroups = collect($response['data'] ?? [])->map(function($groupData) {
                    // Get all participant JIDs (including @lid)
                    $participantJIDs = collect($groupData['Participants'] ?? [])
                        ->pluck('JID')
                        ->filter()
                        ->values()
                        ->all();
                    
                    // Generate group avatar
                    $groupPhoto = $this->generateGroupAvatar($groupData['Name'] ?? 'Grupo');
                    
                    return [
                        'jid' => $groupData['JID'] ?? '',
                        'name' => $groupData['Name'] ?? 'Grupo sem nome',
                        'participants_count' => count($participantJIDs),
                        'participant_jids' => $participantJIDs,
                        'participants' => collect($groupData['Participants'] ?? [])->map(function($participant) {
                            return [
                                'JID' => $participant['JID'] ?? '',
                                'PhoneNumber' => $participant['PhoneNumber'] ?? $participant['JID'] ?? '',
                            ];
                        })->toArray(),
                        'is_announce' => $groupData['IsAnnounce'] ?? false,
                        'created_at' => $groupData['CreatedAt'] ?? now(),
                        'photo' => $groupPhoto,
                        'from_database' => false,
                    ];
                });
            } else {
                $apiError = true;
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching groups for mass sending: ' . $e->getMessage());
            $apiError = true;
        }
        
        // Filter only groups with valid participants (real phone numbers)
        $validGroups = [];
        foreach ($wuzapiGroups as $wuzapiGroup) {
            $validParticipants = [];
            foreach ($wuzapiGroup['participants'] as $participant) {
                // Check if it's an array and has PhoneNumber
                if (is_array($participant) && isset($participant['PhoneNumber'])) {
                    $phoneNumber = $participant['PhoneNumber'];
                    // Accept only phones that end with @s.whatsapp.net
                    if (str_ends_with($phoneNumber, '@s.whatsapp.net')) {
                        $validParticipants[] = $phoneNumber;
                    }
                }
            }
            
            if (!empty($validParticipants)) {
                $wuzapiGroup['valid_participants'] = $validParticipants;
                $wuzapiGroup['valid_count'] = count($validParticipants);
                $validGroups[] = $wuzapiGroup;
            }
        }
        
        return view('mass-sendings.create', compact('validGroups', 'apiError') + ['group' => $originalGroup]);
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
                if (strlen($initials) >= 2) break; // Máximo 2 letras
            }
        }
        
        // Se não conseguiu gerar iniciais, usa "G"
        if (empty($initials)) {
            $initials = 'G';
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

    private function addContactsFromApi(Group $group, array $contactJids)
    {
        $user = $group->user;
        $wuzapiService = new WuzapiService($user->api_token);
        $allContacts = $wuzapiService->getContacts();
        
        if (!$allContacts['success']) {
            return;
        }

        foreach ($allContacts['data'] as $jid => $contact) {
            if (in_array($jid, $contactJids)) {
                GroupContact::create([
                    'group_id' => $group->id,
                    'contact_jid' => $jid,
                    'contact_name' => $contact['PushName'] ?? $contact['name'] ?? null,
                    'contact_phone' => explode('@', $jid)[0] ?? '',
                ]);
            }
        }
    }

    private function addManualContacts(Group $group, array $manualContacts)
    {
        foreach ($manualContacts as $contact) {
            if (!empty($contact['phone'])) {
                // Create JID from phone number
                $jid = $contact['phone'] . '@s.whatsapp.net';
                
                GroupContact::create([
                    'group_id' => $group->id,
                    'contact_jid' => $jid,
                    'contact_name' => $contact['name'] ?? null,
                    'contact_phone' => $contact['phone'],
                ]);
            }
        }
    }
}