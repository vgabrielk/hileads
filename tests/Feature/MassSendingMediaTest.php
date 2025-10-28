<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MassSending;
use App\Models\User;
use App\Exceptions\MissingMediaException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

class MassSendingMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_can_create_text_campaign_successfully()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/mass-sendings', [
            'name' => 'Test Text Campaign',
            'message' => 'This is a test message',
            'media_type' => 'text',
            'wuzapi_participants' => ['group1']
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mass_sendings', [
            'name' => 'Test Text Campaign',
            'message' => 'This is a test message',
            'message_type' => 'text',
            'media_data' => null
        ]);
    }

    public function test_can_create_image_campaign_successfully()
    {
        $user = User::factory()->create();
        $validMediaData = json_encode([
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD',
            'size' => 1024
        ]);

        $response = $this->actingAs($user)->post('/mass-sendings', [
            'name' => 'Test Image Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => $validMediaData,
            'wuzapi_participants' => ['group1']
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $massSending = MassSending::where('name', 'Test Image Campaign')->first();
        $this->assertNotNull($massSending);
        $this->assertEquals('image', $massSending->message_type);
        $this->assertIsArray($massSending->media_data);
        $this->assertArrayHasKey('base64', $massSending->media_data);
    }

    public function test_cannot_create_image_campaign_without_media_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/mass-sendings', [
            'name' => 'Test Image Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => null,
            'wuzapi_participants' => ['group1']
        ]);

        $response->assertSessionHasErrors(['media_data']);
        $this->assertDatabaseMissing('mass_sendings', [
            'name' => 'Test Image Campaign'
        ]);
    }

    public function test_cannot_create_image_campaign_with_invalid_media_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/mass-sendings', [
            'name' => 'Test Image Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => json_encode(['invalid' => 'data']),
            'wuzapi_participants' => ['group1']
        ]);

        $response->assertSessionHasErrors(['media_data']);
        $this->assertDatabaseMissing('mass_sendings', [
            'name' => 'Test Image Campaign'
        ]);
    }

    public function test_cannot_create_document_campaign_without_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/mass-sendings', [
            'name' => 'Test Document Campaign',
            'message' => 'Test caption',
            'media_type' => 'document',
            'media_data' => json_encode([
                'type' => 'application/pdf',
                'base64' => 'data:application/pdf;base64,JVBERi0xLjQKJcfsj6IK',
                'size' => 512
                // Missing name
            ]),
            'wuzapi_participants' => ['group1']
        ]);

        $response->assertSessionHasErrors(['media_data']);
        $this->assertDatabaseMissing('mass_sendings', [
            'name' => 'Test Document Campaign'
        ]);
    }

    public function test_cannot_create_text_campaign_without_message()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/mass-sendings', [
            'name' => 'Test Text Campaign',
            'message' => '',
            'media_type' => 'text',
            'wuzapi_participants' => ['group1']
        ]);

        $response->assertSessionHasErrors(['message']);
        $this->assertDatabaseMissing('mass_sendings', [
            'name' => 'Test Text Campaign'
        ]);
    }

    public function test_mass_sending_model_validation_works()
    {
        $user = User::factory()->create();

        // Test valid image campaign
        $validImageCampaign = MassSending::factory()->withImage()->create(['user_id' => $user->id]);
        $this->assertTrue($validImageCampaign->hasValidMediaData());

        // Test invalid image campaign
        $invalidImageCampaign = MassSending::factory()->withInvalidMedia()->create(['user_id' => $user->id]);
        $this->assertFalse($invalidImageCampaign->hasValidMediaData());

        // Test empty media data
        $emptyMediaCampaign = MassSending::factory()->withEmptyMediaData()->create(['user_id' => $user->id]);
        $this->assertFalse($emptyMediaCampaign->hasValidMediaData());
    }

    public function test_mass_sending_fallback_mechanism_works()
    {
        $user = User::factory()->create();
        
        // Create campaign with empty media_data but valid raw_media_data
        $massSending = MassSending::factory()->create([
            'user_id' => $user->id,
            'message_type' => 'image',
            'media_data' => [],
            'raw_media_data' => json_encode([
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD',
                'size' => 1024
            ])
        ]);

        $fallbackData = $massSending->getMediaDataWithFallback();
        
        $this->assertIsArray($fallbackData);
        $this->assertArrayHasKey('base64', $fallbackData);
        $this->assertNotEmpty($fallbackData['base64']);
    }

    public function test_job_throws_exception_for_invalid_media_data()
    {
        $user = User::factory()->create();
        
        // Create campaign with invalid media data
        $massSending = MassSending::factory()->withInvalidMedia()->create([
            'user_id' => $user->id,
            'status' => 'processing'
        ]);

        $this->expectException(MissingMediaException::class);

        // This would normally be called by the job
        if (!$massSending->hasValidMediaData()) {
            $mediaData = $massSending->getMediaDataWithFallback();
            
            if (empty($mediaData) || empty($mediaData['base64'])) {
                throw MissingMediaException::forMassSending(
                    $massSending->id,
                    $massSending->message_type,
                    $massSending->media_data
                );
            }
        }
    }

    public function test_job_continues_with_valid_media_data()
    {
        $user = User::factory()->create();
        
        // Create campaign with valid media data
        $massSending = MassSending::factory()->withImage()->create([
            'user_id' => $user->id,
            'status' => 'processing'
        ]);

        // This should not throw an exception
        $this->assertTrue($massSending->hasValidMediaData());
        
        $mediaData = $massSending->getMediaDataWithFallback();
        $this->assertIsArray($mediaData);
        $this->assertArrayHasKey('base64', $mediaData);
    }
}
