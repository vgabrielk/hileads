<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MassSending;
use App\Models\SentMessage;

class TestCampaignResume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:test-resume {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test campaign resume functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $massSending = MassSending::find($id);
        
        if (!$massSending) {
            $this->error("Mass sending with ID {$id} not found!");
            return 1;
        }
        
        $this->info("📊 Campaign: {$massSending->name}");
        $this->info("📈 Status: {$massSending->status}");
        $this->info("📞 Total contacts: {$massSending->total_contacts}");
        $this->info("📤 Sent count: {$massSending->sent_count}");
        
        // Get sent phone numbers
        $sentPhones = SentMessage::getSentPhoneNumbers('mass_sending', $massSending->id);
        $this->info("✅ Already sent to: " . count($sentPhones) . " contacts");
        
        // Calculate remaining
        $remaining = $massSending->total_contacts - $massSending->sent_count;
        $this->info("⏳ Remaining to send: {$remaining} contacts");
        
        // Show some sent phone numbers
        if (!empty($sentPhones)) {
            $this->info("📱 Sample sent phones:");
            foreach (array_slice($sentPhones, 0, 5) as $phone) {
                $this->line("   - {$phone}");
            }
            if (count($sentPhones) > 5) {
                $this->line("   ... and " . (count($sentPhones) - 5) . " more");
            }
        }
        
        return 0;
    }
}