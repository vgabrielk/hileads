<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'extracted_contact_id',
        'chat_jid',
        'contact_name',
        'contact_phone',
        'last_message_text',
        'last_message_timestamp',
        'last_message_from_me',
        'unread_count',
        'avatar_url',
        'is_active',
    ];

    protected $casts = [
        'last_message_timestamp' => 'datetime',
        'last_message_from_me' => 'boolean',
        'unread_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com o usuário dono da conversa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o contato extraído (se existir).
     */
    public function extractedContact(): BelongsTo
    {
        return $this->belongsTo(ExtractedContact::class);
    }

    /**
     * Scope para filtrar conversas ativas.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar por última mensagem (mais recente primeiro).
     */
    public function scopeLatestMessage($query)
    {
        return $query->orderBy('last_message_timestamp', 'desc');
    }

    /**
     * Scope para filtrar conversas do usuário autenticado.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Atualiza a última mensagem da conversa.
     */
    public function updateLastMessage(string $text, bool $fromMe, ?string $timestamp = null): void
    {
        $this->update([
            'last_message_text' => $text,
            'last_message_timestamp' => $timestamp ? now()->parse($timestamp) : now(),
            'last_message_from_me' => $fromMe,
            'unread_count' => $fromMe ? $this->unread_count : $this->unread_count + 1,
        ]);
    }

    /**
     * Marca todas as mensagens como lidas.
     */
    public function markAsRead(): void
    {
        $this->update(['unread_count' => 0]);
    }

    /**
     * Incrementa o contador de mensagens não lidas.
     */
    public function incrementUnread(): void
    {
        $this->increment('unread_count');
    }

    /**
     * Formata o telefone para exibição.
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->contact_phone);
        
        if (strlen($phone) === 13 && substr($phone, 0, 2) === '55') {
            // Formato brasileiro: +55 (11) 99999-9999
            return '+' . substr($phone, 0, 2) . ' (' . substr($phone, 2, 2) . ') ' . 
                   substr($phone, 4, 5) . '-' . substr($phone, 9);
        }
        
        return $this->contact_phone;
    }

    /**
     * Retorna o nome de exibição (nome do contato ou telefone formatado).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->contact_name ?: $this->formatted_phone;
    }

    /**
     * Retorna a última mensagem truncada para exibição na lista.
     */
    public function getLastMessagePreviewAttribute(): string
    {
        if (!$this->last_message_text) {
            return 'Sem mensagens';
        }

        return strlen($this->last_message_text) > 50 
            ? substr($this->last_message_text, 0, 50) . '...' 
            : $this->last_message_text;
    }

    /**
     * Retorna o tempo relativo da última mensagem.
     */
    public function getLastMessageTimeAttribute(): string
    {
        if (!$this->last_message_timestamp) {
            return '';
        }

        $diff = now()->diffInMinutes($this->last_message_timestamp);
        
        if ($diff < 1) {
            return 'Agora';
        } elseif ($diff < 60) {
            return $diff . 'm';
        } elseif ($diff < 1440) {
            return round($diff / 60) . 'h';
        } elseif ($diff < 10080) {
            return round($diff / 1440) . 'd';
        } else {
            return $this->last_message_timestamp->format('d/m/Y');
        }
    }
}

