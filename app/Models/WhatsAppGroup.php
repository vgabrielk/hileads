<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppGroup extends Model
{
    protected $table = 'whatsapp_groups';
    
    protected $fillable = [
        'user_id',
        'whatsapp_connection_id',
        'group_id',
        'group_name',
        'group_description',
        'group_picture',
        'participants_count',
        'is_extracted',
        'last_extraction',
    ];

    protected $casts = [
        'is_extracted' => 'boolean',
        'last_extraction' => 'datetime',
    ];

    /**
     * Get the user that owns the group.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the WhatsApp connection for the group.
     */
    public function whatsappConnection(): BelongsTo
    {
        // Especifica a FK correta (coluna é 'whatsapp_connection_id')
        return $this->belongsTo(WhatsAppConnection::class, 'whatsapp_connection_id');
    }

    /**
     * Get the extracted contacts for the group.
     */
    public function extractedContacts(): HasMany
    {
        // Especifica FK correta para evitar inferência 'whats_app_group_id'
        return $this->hasMany(ExtractedContact::class, 'whatsapp_group_id');
    }
}
