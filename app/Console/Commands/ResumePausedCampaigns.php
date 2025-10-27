<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MassSending;
use App\Jobs\ProcessMassSendingJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ResumePausedCampaigns extends Command
{
    protected $signature = 'campaigns:resume-paused';
    protected $description = 'Resume paused campaigns that should be running';

    public function handle()
    {
        $this->info('🔍 Checking for paused campaigns to resume...');
        
        // Check mass sending campaigns
        $pausedMassSendings = MassSending::where('status', 'paused')
            ->where(function($query) {
                $query->where('scheduled_at', '<=', now())
                      ->orWhereNull('scheduled_at');
            })
            ->get();
            
        foreach ($pausedMassSendings as $massSending) {
            $this->resumeCampaign($massSending, 'mass_sending');
        }
        
        $this->info('✅ Resume check completed');
    }
    
    private function resumeCampaign($campaign, $type)
    {
        $campaignId = $campaign->id;
        $pauseKey = "{$type}_pause_{$campaignId}";
        
        // Check if campaign is still paused in cache
        if (Cache::get($pauseKey, false)) {
            $this->info("⏸️  {$type} {$campaignId} is still paused in cache - skipping");
            return;
        }
        
        // Check if campaign is still paused in database
        $campaign->refresh();
        if ($campaign->status !== 'paused') {
            $this->info("✅ {$type} {$campaignId} is no longer paused - skipping");
            return;
        }
        
        // Check if there are remaining contacts to send
        $alreadySentPhones = \App\Models\SentMessage::getSentPhoneNumbers($type, $campaignId);
        $totalParticipants = count($campaign->wuzapi_participants ?? []);
        $remainingCount = $totalParticipants - count($alreadySentPhones);
        
        if ($remainingCount <= 0) {
            $this->info("✅ {$type} {$campaignId} has no remaining contacts - marking as completed");
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }
        
        // Resume the campaign
        $this->info("🔄 Resuming {$type} {$campaignId} - {$remainingCount} contacts remaining");
        
        // Update status to processing
        $campaign->update(['status' => 'processing']);
        
        // Clear pause cache
        Cache::forget($pauseKey);
        
        // Dispatch the job
        ProcessMassSendingJob::dispatch($campaign);
        
        Log::info("🔄 Campaign resumed", [
            'type' => $type,
            'id' => $campaignId,
            'remaining_contacts' => $remainingCount
        ]);
    }
}
