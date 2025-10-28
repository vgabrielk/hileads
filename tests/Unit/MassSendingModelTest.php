<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\MassSending;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MassSendingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_valid_media_data_returns_true_for_text_campaigns()
    {
        $massSending = MassSending::factory()->create([
            'message_type' => 'text',
            'media_data' => null
        ]);

        $this->assertTrue($massSending->hasValidMediaData());
    }

    public function test_has_valid_media_data_returns_true_for_empty_message_type()
    {
        $massSending = MassSending::factory()->create([
            'message_type' => '',
            'media_data' => null
        ]);

        $this->assertTrue($massSending->hasValidMediaData());
    }

    public function test_has_valid_media_data_returns_true_for_valid_image_media()
    {
        $massSending = MassSending::factory()->withImage()->create();

        $this->assertTrue($massSending->hasValidMediaData());
    }

    public function test_has_valid_media_data_returns_true_for_valid_video_media()
    {
        $massSending = MassSending::factory()->withVideo()->create();

        $this->assertTrue($massSending->hasValidMediaData());
    }

    public function test_has_valid_media_data_returns_true_for_valid_document_media()
    {
        $massSending = MassSending::factory()->withDocument()->create();

        $this->assertTrue($massSending->hasValidMediaData());
    }

    public function test_has_valid_media_data_returns_false_for_empty_media_data()
    {
        $massSending = MassSending::factory()->withEmptyMediaData()->create();

        $this->assertFalse($massSending->hasValidMediaData());
    }

    public function test_has_valid_media_data_returns_false_for_null_media_data()
    {
        $massSending = MassSending::factory()->withNullMediaData()->create();

        $this->assertFalse($massSending->hasValidMediaData());
    }

    public function test_has_valid_media_data_returns_false_for_invalid_media_data()
    {
        $massSending = MassSending::factory()->withInvalidMedia()->create();

        $this->assertFalse($massSending->hasValidMediaData());
    }

    public function test_has_valid_media_data_returns_false_for_document_without_name()
    {
        $massSending = MassSending::factory()->create([
            'message_type' => 'document',
            'media_data' => [
                'base64' => 'data:application/pdf;base64,JVBERi0xLjQKJcfsj6IK',
                'type' => 'application/pdf',
                'size' => 512
                // Missing 'name' field
            ]
        ]);

        $this->assertFalse($massSending->hasValidMediaData());
    }

    public function test_get_media_data_with_fallback_returns_media_data_when_available()
    {
        $massSending = MassSending::factory()->withImage()->create();

        $mediaData = $massSending->getMediaDataWithFallback();

        $this->assertIsArray($mediaData);
        $this->assertArrayHasKey('base64', $mediaData);
        $this->assertNotEmpty($mediaData['base64']);
    }

    public function test_get_media_data_with_fallback_returns_empty_array_when_no_data()
    {
        $massSending = MassSending::factory()->withEmptyMediaData()->create();

        $mediaData = $massSending->getMediaDataWithFallback();

        $this->assertIsArray($mediaData);
        $this->assertEmpty($mediaData);
    }

    public function test_media_data_casting_works_correctly()
    {
        $mediaData = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'base64' => 'data:image/jpeg;base64,test',
            'size' => 1024
        ];

        $massSending = MassSending::factory()->create([
            'media_data' => $mediaData
        ]);

        $this->assertIsArray($massSending->media_data);
        $this->assertEquals($mediaData, $massSending->media_data);
    }

    public function test_media_data_serialization_works_correctly()
    {
        $mediaData = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'base64' => 'data:image/jpeg;base64,test',
            'size' => 1024
        ];

        $massSending = new MassSending();
        $massSending->media_data = $mediaData;

        $this->assertIsString($massSending->getAttributes()['media_data']);
        $this->assertEquals(json_encode($mediaData), $massSending->getAttributes()['media_data']);
    }
}
