<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtractedContact extends Model
{
    protected $table = 'extracted_contacts';
    
    protected $fillable = [
        'user_id',
        'whatsapp_group_id',
        'phone_number',
        'contact_name',
        'contact_picture',
        'is_contacted',
        'contacted_at',
        'notes',
        'status',
    ];

    protected $casts = [
        'is_contacted' => 'boolean',
        'contacted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the contact.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the WhatsApp group for the contact.
     */
    public function whatsappGroup(): BelongsTo
    {
        return $this->belongsTo(WhatsAppGroup::class);
    }
}
