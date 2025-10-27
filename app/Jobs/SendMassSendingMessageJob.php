<?php

namespace App\Jobs;

use App\Models\MassSending;
use App\Models\ExtractedContact;
use App\Models\SentMessage;
use App\Services\WuzapiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendMassSendingMessageJob implements ShouldQueue
{
    use Queueable;

    public $massSendingId;
    public $contactId;
    public $message;

    /**
     * Create a new job instance.
     */
    public function __construct(int $massSendingId, int $contactId, string $message)
    {
        $this->campaignId = $massSendingId;
        $this->contactId = $contactId;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Use select to get only needed fields
            $massSending = MassSending::select('id', 'user_id')->find($this->campaignId);
            $contact = ExtractedContact::select('id', 'phone_number')->find($this->contactId);
            
            if (!$massSending || !$contact) {
                Log::warning("Campaign or contact not found", [
                    'campaign_id' => $this->campaignId,
                    'contact_id' => $this->contactId
                ]);
                return;
            }

            // Get user's API token without loading full user object
            $user = \App\Models\User::select('api_token')->find($massSending->user_id);
            if (!$user || !$user->api_token) {
                Log::error("User or API token not found", ['user_id' => $massSending->user_id]);
                return;
            }

            // Clean phone number
            $phone = preg_replace('/\D+/', '', $contact->phone_number ?? '');
            if (!$phone) {
                Log::warning("Invalid phone number for contact", ['contact_id' => $this->contactId]);
                return;
            }

            // Check if message was already sent to this phone number
            if (SentMessage::wasAlreadySent('mass_sending', $this->campaignId, $phone)) {
                Log::info("⏭️  Skipping already sent message", [
                    'phone' => $phone,
                    'mass_sending_id' => $this->campaignId,
                    'contact_id' => $this->contactId
                ]);
                return;
            }

            // Send message via Wuzapi
            $wuzapiService = new WuzapiService($user->api_token);
            $result = $wuzapiService->sendTextMessage($phone, $this->message);
            
            if ($result['success'] ?? false) {
                // Update campaign stats
                $massSending->increment('sent_count');
                
                // Record the sent message to prevent duplicates
                SentMessage::recordSent(
                    'mass_sending',
                    $this->campaignId,
                    $phone,
                    null,
                    $result['data']['Id'] ?? null,
                    'sent',
                    $result
                );
                
                // Mark contact as contacted
                $contact->update([
                    'is_contacted' => true,
                    'contacted_at' => now(),
                    'status' => 'contacted'
                ]);
                
                Log::info("Message sent successfully", [
                    'campaign_id' => $this->campaignId,
                    'contact_id' => $this->contactId,
                    'phone' => $phone
                ]);
            } else {
                // Record failed message
                SentMessage::recordSent(
                    'mass_sending',
                    $this->campaignId,
                    $phone,
                    null,
                    null,
                    'failed',
                    $result
                );
                
                Log::error("Failed to send message", [
                    'campaign_id' => $this->campaignId,
                    'contact_id' => $this->contactId,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
            
            // Free memory
            unset($massSending, $contact, $user, $wuzapiService, $result, $phone);
            
        } catch (\Exception $e) {
            Log::error("SendMassSendingMessageJob failed", [
                'campaign_id' => $this->campaignId,
                'contact_id' => $this->contactId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}
