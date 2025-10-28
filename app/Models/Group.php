<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'contacts_count',
    ];

    protected $casts = [
        'contacts_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groupContacts(): HasMany
    {
        return $this->hasMany(GroupContact::class);
    }

    public function massSendings(): HasMany
    {
        return $this->hasMany(MassSending::class);
    }

    // Get contacts from API that are in this group
    public function getContactsFromApi()
    {
        $user = $this->user;
        $wuzapiService = new \App\Services\WuzapiService($user->api_token);
        $allContactsResponse = $wuzapiService->getContacts();
        
        $allContacts = $allContactsResponse['success'] ? $allContactsResponse['data'] : [];
        
        $groupContacts = $this->groupContacts()->get();
        
        $filteredContacts = [];
        foreach ($groupContacts as $groupContact) {
            $jid = $groupContact->contact_jid;
            
            // Tentar obter dados da API primeiro
            if (isset($allContacts[$jid])) {
                $contact = $allContacts[$jid];
                $filteredContacts[] = [
                    'jid' => $jid,
                    'pushName' => $contact['PushName'] ?? $groupContact->contact_name ?? null,
                    'name' => $groupContact->contact_name ?? $contact['name'] ?? null,
                    'phone' => $groupContact->contact_phone ?? explode('@', $jid)[0] ?? '',
                ];
            } else {
                // Se não estiver na API, usar dados salvos no banco
                $filteredContacts[] = [
                    'jid' => $jid,
                    'pushName' => $groupContact->contact_name,
                    'name' => $groupContact->contact_name,
                    'phone' => $groupContact->contact_phone ?? explode('@', $jid)[0] ?? '',
                ];
            }
        }
        
        return $filteredContacts;
    }

    // Update contacts count
    public function updateContactsCount(): void
    {
        $this->update(['contacts_count' => $this->groupContacts()->count()]);
    }
}
