<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SentMessage;
use App\Models\MassSending;
use Illuminate\Support\Facades\DB;

class CheckDuplicateMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:check-duplicates {--campaign-id=} {--cleanup : Remove duplicate entries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and optionally clean duplicate messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for duplicate messages...');
        
        $campaignId = $this->option('campaign-id');
        $cleanup = $this->option('cleanup');
        
        if ($campaignId) {
            $this->checkCampaignDuplicates($campaignId, $cleanup);
        } else {
            $this->checkAllDuplicates($cleanup);
        }
        
        return 0;
    }
    
    /**
     * Check duplicates for a specific campaign
     */
    private function checkCampaignDuplicates($campaignId, $cleanup = false)
    {
        $this->info("📊 Checking campaign ID: {$campaignId}");
        
        // Check campaigns
        $campaignDuplicates = SentMessage::where('campaign_type', 'campaign')
            ->where('campaign_id', $campaignId)
            ->select('phone_number', DB::raw('COUNT(*) as count'))
            ->groupBy('phone_number')
            ->having('count', '>', 1)
            ->get();
            
        // Check mass sendings
        $massSendingDuplicates = SentMessage::where('campaign_type', 'mass_sending')
            ->where('campaign_id', $campaignId)
            ->select('phone_number', DB::raw('COUNT(*) as count'))
            ->groupBy('phone_number')
            ->having('count', '>', 1)
            ->get();
        
        $this->displayDuplicates('Campaign', $campaignDuplicates, $cleanup);
        $this->displayDuplicates('Mass Sending', $massSendingDuplicates, $cleanup);
    }
    
    /**
     * Check all duplicates
     */
    private function checkAllDuplicates($cleanup = false)
    {
        $this->info('📊 Checking all campaigns and mass sendings...');
        
        // Get all campaigns with duplicates
        $campaignDuplicates = SentMessage::where('campaign_type', 'campaign')
            ->select('campaign_id', 'phone_number', DB::raw('COUNT(*) as count'))
            ->groupBy('campaign_id', 'phone_number')
            ->having('count', '>', 1)
            ->get()
            ->groupBy('campaign_id');
            
        // Get all mass sendings with duplicates
        $massSendingDuplicates = SentMessage::where('campaign_type', 'mass_sending')
            ->select('campaign_id', 'phone_number', DB::raw('COUNT(*) as count'))
            ->groupBy('campaign_id', 'phone_number')
            ->having('count', '>', 1)
            ->get()
            ->groupBy('campaign_id');
        
        $this->info("📈 Found duplicates in " . $campaignDuplicates->count() . " campaigns");
        $this->info("📈 Found duplicates in " . $massSendingDuplicates->count() . " mass sendings");
        
        if ($cleanup) {
            $this->cleanupDuplicates($campaignDuplicates, 'campaign');
            $this->cleanupDuplicates($massSendingDuplicates, 'mass_sending');
        } else {
            $this->warn('💡 Use --cleanup flag to remove duplicates');
        }
    }
    
    /**
     * Display duplicates for a campaign type
     */
    private function displayDuplicates($type, $duplicates, $cleanup = false)
    {
        if ($duplicates->isEmpty()) {
            $this->info("✅ No duplicates found for {$type}");
            return;
        }
        
        $this->warn("⚠️  Found {$duplicates->count()} duplicate phone numbers in {$type}:");
        
        $table = [];
        foreach ($duplicates as $duplicate) {
            $table[] = [
                'Phone' => $duplicate->phone_number,
                'Count' => $duplicate->count,
                'Type' => $type
            ];
        }
        
        $this->table(['Phone', 'Count', 'Type'], $table);
        
        if ($cleanup) {
            $this->cleanupDuplicates($duplicates, strtolower(str_replace(' ', '_', $type)));
        }
    }
    
    /**
     * Clean up duplicates
     */
    private function cleanupDuplicates($duplicates, $type)
    {
        $this->info("🧹 Cleaning up duplicates for {$type}...");
        
        $totalRemoved = 0;
        
        foreach ($duplicates as $duplicate) {
            if (is_array($duplicate)) {
                // Handle grouped duplicates
                foreach ($duplicate as $item) {
                    $removed = $this->removeDuplicateEntries($type, $item->campaign_id, $item->phone_number);
                    $totalRemoved += $removed;
                }
            } else {
                // Handle single campaign duplicates
                $removed = $this->removeDuplicateEntries($type, $duplicate->campaign_id ?? null, $duplicate->phone_number);
                $totalRemoved += $removed;
            }
        }
        
        $this->info("✅ Removed {$totalRemoved} duplicate entries");
    }
    
    /**
     * Remove duplicate entries, keeping only the first one
     */
    private function removeDuplicateEntries($type, $campaignId, $phoneNumber)
    {
        $entries = SentMessage::where('campaign_type', $type)
            ->where('campaign_id', $campaignId)
            ->where('phone_number', $phoneNumber)
            ->orderBy('created_at', 'asc')
            ->get();
        
        if ($entries->count() <= 1) {
            return 0;
        }
        
        // Keep the first entry, remove the rest
        $toRemove = $entries->skip(1);
        $removedCount = $toRemove->count();
        
        foreach ($toRemove as $entry) {
            $entry->delete();
        }
        
        return $removedCount;
    }
}
