<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConnection extends Model
{
    protected $table = 'whatsapp_connections';
    
    protected $fillable = [
        'user_id',
        'phone_number',
        'instance_id',
        'status',
        'connection_data',
        'last_sync',
    ];

    protected $casts = [
        'connection_data' => 'array',
        'last_sync' => 'datetime',
    ];

    /**
     * Get the user that owns the connection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the WhatsApp groups for the connection.
     */
    public function whatsappGroups(): HasMany
    {
        // Especifica a FK correta para evitar o default errado (whats_app_connection_id)
        return $this->hasMany(WhatsAppGroup::class, 'whatsapp_connection_id');
    }
}
