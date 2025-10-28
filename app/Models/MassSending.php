<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MassSending extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'whatsapp_connection_id',
        'name',
        'message',
        'message_type',
        'media_data',
        'status',
        'contact_ids',
        'wuzapi_participants',
        'total_contacts',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'read_count',
        'replied_count',
        'failed_count',
        'scheduled_at',
        'started_at',
        'completed_at',
        'failed_at',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'cancelled_at' => 'datetime',
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
        
        if (is_array($value)) {
            return $value;
        }
        
        return [];
    }
    
    /**
     * Set the media data attribute with proper encoding
     */
    public function setMediaDataAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['media_data'] = json_encode($value);
        } else {
            $this->attributes['media_data'] = $value;
        }
    }

    /**
     * Check if the campaign has valid media data
     */
    public function hasValidMediaData(): bool
    {
        if ($this->message_type === 'text' || empty($this->message_type)) {
            return true; // Text campaigns don't need media data
        }

        $mediaData = $this->media_data;
        
        if (empty($mediaData) || !is_array($mediaData)) {
            return false;
        }

        // Check if base64 data exists and is valid
        if (empty($mediaData['base64'])) {
            return false;
        }

        // Validate base64 format
        if (!preg_match('/^data:[^;]+;base64,/', $mediaData['base64'])) {
            return false;
        }

        // For documents, check if name is provided
        if ($this->message_type === 'document' && empty($mediaData['name'])) {
            return false;
        }

        return true;
    }

    /**
     * Get media data with fallback to raw_media_data if available
     */
    public function getMediaDataWithFallback(): array
    {
        $mediaData = $this->media_data;
        
        // If media_data is empty but we have raw_media_data, try to use it
        if (empty($mediaData) && !empty($this->raw_media_data)) {
            $rawData = is_string($this->raw_media_data) 
                ? json_decode($this->raw_media_data, true) 
                : $this->raw_media_data;
                
            if (is_array($rawData) && !empty($rawData['base64'])) {
                \Log::warning('Using raw_media_data as fallback', [
                    'mass_sending_id' => $this->id,
                    'message_type' => $this->message_type
                ]);
                return $rawData;
            }
        }
        
        return $mediaData ?: [];
    }

    /**
     * Get the user that owns the campaign.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the WhatsApp connection for this campaign.
     */
    public function whatsappConnection(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConnection::class);
    }
}
