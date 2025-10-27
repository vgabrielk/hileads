<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MassSending;
use App\Models\SentMessage;
use Illuminate\Support\Facades\Log;

class FixCampaignStatus extends Command
{
    protected $signature = 'campaigns:fix-status';
    protected $description = 'Fix campaign status inconsistencies';

    public function handle()
    {
        $this->info('🔧 Fixing campaign status inconsistencies...');
        
        $fixedCount = 0;
        
        // Get all campaigns that are marked as completed but have remaining contacts
        $campaigns = MassSending::where('status', 'completed')
            ->whereRaw('total_contacts > sent_count')
            ->get();
            
        foreach ($campaigns as $campaign) {
            $remainingContacts = $campaign->total_contacts - $campaign->sent_count;
            
            if ($remainingContacts > 0) {
                $this->info("🔧 Fixing campaign {$campaign->id}: {$remainingContacts} remaining contacts");
                
                // Update status to paused
                $campaign->update([
                    'status' => 'paused',
                    'completed_at' => null,
                ]);
                
                $fixedCount++;
                
                Log::info("🔧 Fixed campaign status", [
                    'mass_sending_id' => $campaign->id,
                    'total_contacts' => $campaign->total_contacts,
                    'sent_count' => $campaign->sent_count,
                    'remaining' => $remainingContacts
                ]);
            }
        }
        
        // Also check for campaigns that are paused but should be completed
        $pausedCampaigns = MassSending::where('status', 'paused')
            ->whereRaw('total_contacts <= sent_count')
            ->get();
            
        foreach ($pausedCampaigns as $campaign) {
            $this->info("🔧 Fixing campaign {$campaign->id}: should be completed");
            
            // Update status to completed
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            
            $fixedCount++;
            
            Log::info("🔧 Fixed campaign status to completed", [
                'mass_sending_id' => $campaign->id,
                'total_contacts' => $campaign->total_contacts,
                'sent_count' => $campaign->sent_count
            ]);
        }
        
        $this->info("✅ Fixed {$fixedCount} campaigns");
        
        if ($fixedCount > 0) {
            $this->info("💡 You may need to refresh the page to see the changes");
        }
    }
}

