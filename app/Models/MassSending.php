<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MassSending extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'message',
        'message_type',
        'media_data',
        'status',
        'contact_ids',
        'wuzapi_participants',
        'total_contacts',
        'sent_count',
        'delivered_count',
        'read_count',
        'replied_count',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'contact_ids' => 'array',
        'wuzapi_participants' => 'array',
        'media_data' => 'array',
    ];

    /**
     * Get the media data attribute with proper decoding
     */
    public function getMediaDataAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded ?: [];
        }
        
        return $value ?: [];
    }

    /**
     * Get the user that owns the campaign.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
