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
        $allContacts = $wuzapiService->getContacts();
        
        if (!$allContacts['success']) {
            return [];
        }

        $groupContactJids = $this->groupContacts()->pluck('contact_jid')->toArray();
        
        $filteredContacts = [];
        foreach ($allContacts['data'] as $jid => $contact) {
            if (in_array($jid, $groupContactJids)) {
                $filteredContacts[] = [
                    'jid' => $jid,
                    'pushName' => $contact['PushName'] ?? null,
                    'name' => $contact['name'] ?? null,
                    'phone' => explode('@', $jid)[0] ?? '',
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
