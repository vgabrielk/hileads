<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'name',
        'push_name',
        'jid',
        'is_manual', // true if added manually by user
    ];

    protected $casts = [
        'is_manual' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_contacts');
    }

    // Get display name (prefer name, fallback to push_name, then phone)
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->push_name ?: $this->phone_number;
    }
}
