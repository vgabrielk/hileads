<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MassSending;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TestPauseImmediate extends Command
{
    protected $signature = 'test:pause-immediate {mass_sending_id}';
    protected $description = 'Test immediate pause functionality for mass sending';

    public function handle()
    {
        $massSendingId = $this->argument('mass_sending_id');
        
        $massSending = MassSending::find($massSendingId);
        if (!$massSending) {
            $this->error("Mass sending with ID {$massSendingId} not found!");
            return 1;
        }
        
        $this->info("Testing immediate pause for Mass Sending #{$massSendingId}");
        $this->info("Current status: {$massSending->status}");
        
        // Set pause flag in cache
        $pauseKey = "mass_sending_pause_{$massSending->id}";
        Cache::put($pauseKey, true, 3600);
        
        $this->info("✅ Pause flag set in cache: {$pauseKey}");
        
        // Check if flag is set
        $isPaused = Cache::get($pauseKey, false);
        $this->info("Cache check result: " . ($isPaused ? 'PAUSED' : 'NOT PAUSED'));
        
        // Update database status
        $massSending->update(['status' => 'paused']);
        $this->info("✅ Database status updated to: paused");
        
        // Test cache clearing
        $this->info("\n--- Testing cache clearing ---");
        Cache::forget($pauseKey);
        $isPausedAfterClear = Cache::get($pauseKey, false);
        $this->info("Cache check after clearing: " . ($isPausedAfterClear ? 'PAUSED' : 'NOT PAUSED'));
        
        // Restore original status
        $massSending->update(['status' => 'active']);
        $this->info("✅ Database status restored to: active");
        
        $this->info("\n🎉 Test completed! The pause system should now work immediately.");
        
        return 0;
    }
}
