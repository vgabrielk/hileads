<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SentMessage extends Model
{
    protected $fillable = [
        'campaign_type',
        'campaign_id',
        'phone_number',
        'jid',
        'message_id',
        'status',
        'sent_at',
        'response_data'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'response_data' => 'array'
    ];

    /**
     * Check if a message was already sent to a phone number for a specific campaign
     */
    public static function wasAlreadySent(string $campaignType, int $campaignId, string $phoneNumber): bool
    {
        return self::where('campaign_type', $campaignType)
            ->where('campaign_id', $campaignId)
            ->where('phone_number', $phoneNumber)
            ->exists();
    }

    /**
     * Record a sent message
     */
    public static function recordSent(
        string $campaignType,
        int $campaignId,
        string $phoneNumber,
        ?string $jid = null,
        ?string $messageId = null,
        string $status = 'sent',
        ?array $responseData = null
    ): self {
        return self::create([
            'campaign_type' => $campaignType,
            'campaign_id' => $campaignId,
            'phone_number' => $phoneNumber,
            'jid' => $jid,
            'message_id' => $messageId,
            'status' => $status,
            'sent_at' => now(),
            'response_data' => $responseData
        ]);
    }

    /**
     * Get sent messages for a campaign
     */
    public static function getSentForCampaign(string $campaignType, int $campaignId): Builder
    {
        return self::where('campaign_type', $campaignType)
            ->where('campaign_id', $campaignId);
    }

    /**
     * Get sent phone numbers for a campaign
     */
    public static function getSentPhoneNumbers(string $campaignType, int $campaignId): array
    {
        return self::getSentForCampaign($campaignType, $campaignId)
            ->pluck('phone_number')
            ->toArray();
    }
}
