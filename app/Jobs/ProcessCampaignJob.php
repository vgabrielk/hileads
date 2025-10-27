<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MassSending;
use App\Models\SentMessage;
use App\Services\WuzapiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessMassSendingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $massSending;
    protected $user;

    public function __construct(MassSending $massSending)
    {
        $this->massSending = $massSending;
        $this->user = $massSending->user;
    }

    public function handle()
    {
        \Log::info('🚀 ProcessMassSendingJob started', [
            'mass_sending_id' => $this->massSending->id,
            'message_type' => $this->massSending->message_type,
            'has_media_data' => !empty($this->massSending->media_data)
        ]);
        
        $massSending = $this->massSending;
        $user = $this->user;
        
        // Set timeout to prevent infinite execution
        set_time_limit(600); // 10 minutes maximum
        
        // Initialize progress tracking
        $progressKey = "mass_sending_progress_{$massSending->id}";
        $totalParticipants = count($massSending->wuzapi_participants ?? []);
        
        Cache::put($progressKey, [
            'status' => 'processing',
            'total' => $totalParticipants,
            'sent' => 0,
            'failed' => 0,
            'current_message' => 'Iniciando envio das mensagens...',
            'started_at' => now()->toISOString(),
        ], 3600); // Cache for 1 hour
        
        Log::info("🚀 Starting mass sending job", [
            'mass_sending_id' => $massSending->id,
            'mass_sending_name' => $massSending->name,
            'total_participants' => $totalParticipants,
            'message_preview' => substr($massSending->message, 0, 100)
        ]);

        $sentCount = 0;
        $failedCount = 0;
        $jids = $massSending->wuzapi_participants ?? [];
        
        // Filter out already sent contacts if this is a resume
        $alreadySentPhones = SentMessage::getSentPhoneNumbers('mass_sending', $massSending->id);
        if (!empty($alreadySentPhones)) {
            $originalCount = count($jids);
            $jids = array_filter($jids, function($jid) use ($alreadySentPhones) {
                $phone = str_replace('@s.whatsapp.net', '', $jid);
                return !in_array($phone, $alreadySentPhones);
            });
            
            Log::info("🔄 Resuming mass_sending - filtering out already sent contacts", [
                'mass_sending_id' => $massSending->id,
                'original_participants' => $originalCount,
                'already_sent' => count($alreadySentPhones),
                'remaining_to_send' => count($jids)
            ]);
            
            // If no contacts left to send, mark as completed
            if (empty($jids)) {
                Log::info("✅ No remaining contacts to send - marking mass_sending as completed", [
                    'mass_sending_id' => $massSending->id
                ]);
                
                $massSending->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                
                return;
            }
        }
        
        // Process messages in batches of 20
        $batchSize = 20;
        $filteredJids = array_values($jids); // Reindex array and keep it
        $totalBatches = ceil(count($filteredJids) / $batchSize);
        
        $totalJids = count($filteredJids);
        
        for ($batchIndex = 0; $batchIndex < $totalBatches; $batchIndex++) {
            // Check if mass_sending was paused (check cache first for immediate effect)
            $pauseKey = "mass_sending_pause_{$massSending->id}";
            if (\Cache::get($pauseKey, false)) {
                Log::info("⏸️ MassSending paused via cache - stopping processing immediately", [
                    'mass_sending_id' => $massSending->id,
                    'batch_index' => $batchIndex,
                    'total_batches' => $totalBatches
                ]);
                
                // Schedule automatic resume after 30 seconds
                $this->scheduleResume($massSending, 30);
                break;
            }
            
            // Also check database status
            $massSending->refresh();
            if ($massSending->status === 'paused') {
                Log::info("⏸️ MassSending paused via database - stopping processing", [
                    'mass_sending_id' => $massSending->id,
                    'batch_index' => $batchIndex,
                    'total_batches' => $totalBatches
                ]);
                break;
            }
            
            $batchStart = $batchIndex * $batchSize;
            $batchEnd = min($batchStart + $batchSize, $totalJids);
            
            // Get batch data from filtered contacts (only unsent ones)
            $batchJids = array_slice($filteredJids, $batchStart, $batchEnd - $batchStart);
            
            Log::info("📦 Processing batch " . ($batchIndex + 1) . "/{$totalBatches}", [
                'batch_start' => $batchStart + 1,
                'batch_end' => $batchEnd,
                'batch_size' => count($batchJids)
            ]);
            
            // Send messages in current batch
            foreach ($batchJids as $index => $jid) {
                $globalIndex = $batchStart + $index;
                
                // Check if mass_sending was paused during message processing (every 5 messages)
                if ($index % 5 === 0) {
                    $pauseKey = "mass_sending_pause_{$massSending->id}";
                    if (\Cache::get($pauseKey, false)) {
                        Log::info("⏸️ MassSending paused via cache during message processing - stopping immediately", [
                            'mass_sending_id' => $massSending->id,
                            'current_index' => $globalIndex,
                            'batch_index' => $batchIndex
                        ]);
                        break 2; // Break out of both loops
                    }
                    
                    // Also check database status
                    $massSending->refresh();
                    if ($massSending->status === 'paused') {
                        Log::info("⏸️ MassSending paused via database during message processing - stopping", [
                            'mass_sending_id' => $massSending->id,
                            'current_index' => $globalIndex,
                            'batch_index' => $batchIndex
                        ]);
                        break 2; // Break out of both loops
                    }
                }
                
                try {
                    $phone = str_replace('@s.whatsapp.net', '', $jid);
                    
                    // Check if message was already sent to this phone number
                    if (SentMessage::wasAlreadySent('mass_sending', $massSending->id, $phone)) {
                        Log::info("⏭️  Skipping already sent message", [
                            'phone' => $phone,
                            'mass_sending_id' => $massSending->id,
                            'index' => $globalIndex + 1
                        ]);
                        continue;
                    }
                    
                    // Update progress
                    Cache::put($progressKey, [
                        'status' => 'processing',
                        'total' => $totalParticipants,
                        'sent' => $sentCount,
                        'failed' => $failedCount,
                        'current_message' => "Enviando para {$phone}... (Lote " . ($batchIndex + 1) . "/{$totalBatches})",
                        'current_index' => $globalIndex + 1,
                        'started_at' => Cache::get($progressKey)['started_at'] ?? now()->toISOString(),
                    ], 3600);
                    
                    Log::info("📱 Sending message to participant", [
                        'jid' => $jid,
                        'phone' => $phone,
                        'index' => $globalIndex + 1,
                        'total' => $totalParticipants,
                        'batch' => $batchIndex + 1
                    ]);
                    
                    // Send message using phone number
                    $service = new WuzapiService($user->api_token);
                    
                    // Check message type and send accordingly
                    if ($massSending->message_type === 'text' || empty($massSending->message_type)) {
                        // Send text message
                        $result = $service->sendTextMessage($phone, $massSending->message);
                    } else {
                        // Send media message
                        $mediaData = $massSending->media_data;
                        if (!$mediaData || empty($mediaData['base64'])) {
                            Log::error("❌ No media data found for mass_sending", [
                                'mass_sending_id' => $massSending->id,
                                'message_type' => $massSending->message_type
                            ]);
                            $failedCount++;
                            continue;
                        }
                        
                        $caption = $massSending->message; // Use message as caption (this contains the media caption)
                        
                        // Generate unique ID for this message
                        $messageId = 'mass_sending_' . $massSending->id . '_' . $globalIndex . '_' . time();
                        
                        switch ($massSending->message_type) {
                            case 'image':
                                $result = $service->sendImageMessage($phone, $mediaData['base64'], $caption, $messageId);
                                break;
                            case 'video':
                                $result = $service->sendVideoMessage($phone, $mediaData['base64'], $caption, $messageId);
                                break;
                            case 'audio':
                                $result = $service->sendAudioMessage($phone, $mediaData['base64'], $messageId);
                                break;
                            case 'document':
                                $result = $service->sendDocumentMessage($phone, $mediaData['base64'], $mediaData['name'], $messageId);
                                break;
                            default:
                                Log::error("❌ Unknown message type", [
                                    'message_type' => $massSending->message_type
                                ]);
                                $failedCount++;
                                continue 2;
                        }
                    }
                    
                    Log::info("📡 API Response", [
                        'jid' => $jid,
                        'phone' => $phone,
                        'response' => $result
                    ]);
                    
                    if ($result['success'] ?? false) {
                        $sentCount++;
                        $massSending->increment('sent_count');
                        
                        // Record the sent message to prevent duplicates
                        SentMessage::recordSent(
                            'mass_sending',
                            $massSending->id,
                            $phone,
                            $jid,
                            $result['data']['Id'] ?? null,
                            'sent',
                            $result
                        );
                        
                        // Also increment delivered if API confirms
                        if (($result['data']['Details'] ?? '') === 'Sent') {
                            $massSending->increment('delivered_count');
                            
                            // Update status to delivered
                            SentMessage::where('mass_sending_type', 'mass_sending')
                                ->where('mass_sending_id', $massSending->id)
                                ->where('phone_number', $phone)
                                ->update(['status' => 'delivered']);
                        }
                        
                        Log::info("✅ Message delivered", [
                            'jid' => $jid,
                            'phone' => $phone,
                            'message_id' => $result['data']['Id'] ?? null,
                            'status' => $result['data']['Details'] ?? null
                        ]);
                    } else {
                        $failedCount++;
                        
                        // Record failed message
                        SentMessage::recordSent(
                            'mass_sending',
                            $massSending->id,
                            $phone,
                            $jid,
                            null,
                            'failed',
                            $result
                        );
                        
                        Log::error("❌ Failed to send", [
                            'jid' => $jid,
                            'phone' => $phone,
                            'error' => $result['message'] ?? 'Unknown error',
                            'response' => $result
                        ]);
                    }
                    
                    // Small delay between messages in the same batch
                    usleep(100000); // 0.1 second between messages in batch
                    
                    // Free memory after each message
                    unset($result, $service);
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("💥 Exception", [
                        'jid' => $jid,
                        'phone' => $phone ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Free memory for current message variables
                unset($phone, $jid);
            }
            
            // Free memory after each batch
            unset($batchJids);
            
            // Force garbage collection
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            // Wait 5 seconds between batches (except for the last batch)
            if ($batchIndex < $totalBatches - 1) {
                Log::info("⏳ Waiting 5 seconds before next batch...", [
                    'current_batch' => $batchIndex + 1,
                    'total_batches' => $totalBatches
                ]);
                sleep(5);
            }
        }
        
        // Check if all contacts were actually processed
        $remainingContacts = $massSending->total_contacts - $massSending->sent_count;
        
        // Also check if we actually sent any messages in this job run
        $actuallyProcessed = $sentCount > 0 || $failedCount > 0;
        
        if ($remainingContacts <= 0) {
            // All contacts processed, mark as completed
            $massSending->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            
            Log::info("✅ Mass sending truly completed - all contacts processed", [
                'mass_sending_id' => $massSending->id,
                'total_contacts' => $massSending->total_contacts,
                'sent_count' => $massSending->sent_count,
                'remaining' => $remainingContacts
            ]);
        } else if (!$actuallyProcessed) {
            // No messages were sent in this run, but there are still contacts
            // This means all remaining contacts were already sent previously
            // Check if we can mark as completed
            $alreadySentPhones = SentMessage::getSentPhoneNumbers('mass_sending', $massSending->id);
            $totalParticipants = count($massSending->wuzapi_participants ?? []);
            $actuallyRemaining = $totalParticipants - count($alreadySentPhones);
            
            if ($actuallyRemaining <= 0) {
                // All contacts were actually sent, mark as completed
                $massSending->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                
                Log::info("✅ Mass sending completed - all contacts were already sent", [
                    'mass_sending_id' => $massSending->id,
                    'total_contacts' => $massSending->total_contacts,
                    'sent_count' => $massSending->sent_count,
                    'actually_remaining' => $actuallyRemaining
                ]);
            } else {
                // Still have contacts to process, mark as paused for resume
                $massSending->update([
                    'status' => 'paused',
                ]);
                
                Log::info("⏸️ Mass sending paused - not all contacts processed", [
                    'mass_sending_id' => $massSending->id,
                    'total_contacts' => $massSending->total_contacts,
                    'sent_count' => $massSending->sent_count,
                    'remaining' => $remainingContacts,
                    'actually_remaining' => $actuallyRemaining
                ]);
            }
        } else {
            // Still have contacts to process, mark as paused for resume
            $massSending->update([
                'status' => 'paused',
            ]);
            
            Log::info("⏸️ Mass sending paused - not all contacts processed", [
                'mass_sending_id' => $massSending->id,
                'total_contacts' => $massSending->total_contacts,
                'sent_count' => $massSending->sent_count,
                'remaining' => $remainingContacts
            ]);
        }
        
        // Final progress update
        $finalStatus = $massSending->status;
        $finalMessage = $finalStatus === 'completed'
            ? "Campanha concluída! {$sentCount} enviadas, {$failedCount} falharam"
            : "Campanha pausada! {$sentCount} enviadas, {$failedCount} falharam, {$remainingContacts} restantes";
            
        Cache::put($progressKey, [
            'status' => $finalStatus,
            'total' => $totalParticipants,
            'sent' => $sentCount,
            'failed' => $failedCount,
            'current_message' => $finalMessage,
            'completed_at' => $remainingContacts <= 0 ? now()->toISOString() : null,
        ], 3600);
        
        Log::info("✨ MassSending job finished", [
            'mass_sending_id' => $massSending->id,
            'sent' => $sentCount,
            'failed' => $failedCount,
            'total' => $totalParticipants,
            'remaining' => $remainingContacts,
            'status' => $finalStatus
        ]);
    }
    
    public function failed(\Throwable $exception)
    {
        $massSending = $this->mass_sending;
        $progressKey = "mass_sending_progress_{$massSending->id}";
        
        // Update progress with error
        Cache::put($progressKey, [
            'status' => 'failed',
            'total' => count($massSending->wuzapi_participants ?? []),
            'sent' => 0,
            'failed' => 0,
            'current_message' => 'Erro ao processar campanha: ' . $exception->getMessage(),
            'failed_at' => now()->toISOString(),
        ], 3600);
        
        // Update mass_sending status
        $massSending->update([
            'status' => 'failed',
        ]);
        
        Log::error("💥 MassSending job failed", [
            'mass_sending_id' => $massSending->id,
            'error' => $exception->getMessage()
        ]);
    }
    
    private function scheduleResume($massSending, $delaySeconds)
    {
        // Schedule a delayed job to check and resume the mass_sending
        \App\Jobs\CheckAndResumeMassSendingJob::dispatch($massSending, 'mass_sending')
            ->delay(now()->addSeconds($delaySeconds));
            
        Log::info("⏰ Scheduled automatic resume check", [
            'mass_sending_id' => $massSending->id,
            'delay_seconds' => $delaySeconds
        ]);
    }
}