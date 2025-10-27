<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MassSending;
use App\Jobs\ProcessMassSendingJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckAndResumeCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $type;

    public function __construct($campaign, $type)
    {
        $this->campaign = $campaign;
        $this->type = $type;
    }

    public function handle()
    {
        $campaign = $this->campaign;
        $type = $this->type;
        $campaignId = $campaign->id;
        $pauseKey = "{$type}_pause_{$campaignId}";
        
        Log::info("🔍 Checking if campaign should be resumed", [
            'type' => $type,
            'id' => $campaignId,
            'pause_key' => $pauseKey
        ]);
        
        // Check if campaign is still paused in cache
        if (Cache::get($pauseKey, false)) {
            Log::info("⏸️ Campaign still paused in cache - will check again later", [
                'type' => $type,
                'id' => $campaignId
            ]);
            
            // Schedule another check in 30 seconds
            self::dispatch($campaign, $type)->delay(now()->addSeconds(30));
            return;
        }
        
        // Check if campaign is still paused in database
        $campaign->refresh();
        if ($campaign->status !== 'paused') {
            Log::info("✅ Campaign no longer paused - skipping resume", [
                'type' => $type,
                'id' => $campaignId,
                'status' => $campaign->status
            ]);
            return;
        }
        
        // Check if there are remaining contacts to send
        $alreadySentPhones = \App\Models\SentMessage::getSentPhoneNumbers($type, $campaignId);
        $totalParticipants = count($campaign->wuzapi_participants ?? []);
        $remainingCount = $totalParticipants - count($alreadySentPhones);
        
        if ($remainingCount <= 0) {
            Log::info("✅ Campaign has no remaining contacts - marking as completed", [
                'type' => $type,
                'id' => $campaignId
            ]);
            
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }
        
        // Resume the campaign
        Log::info("🔄 Resuming campaign automatically", [
            'type' => $type,
            'id' => $campaignId,
            'remaining_contacts' => $remainingCount
        ]);
        
        // Update status to processing
        $campaign->update(['status' => 'processing']);
        
        // Clear pause cache
        Cache::forget($pauseKey);
        
        // Dispatch the job
        ProcessMassSendingJob::dispatch($campaign);
    }
}
