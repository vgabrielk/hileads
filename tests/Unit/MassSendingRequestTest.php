<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\MassSendingRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MassSendingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_passes_for_valid_text_campaign()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test message',
            'media_type' => 'text',
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertFalse($validator->fails());
    }

    public function test_validation_passes_for_valid_image_campaign()
    {
        $validMediaData = json_encode([
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD',
            'size' => 1024
        ]);

        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => $validMediaData,
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertFalse($validator->fails());
    }

    public function test_validation_fails_for_image_campaign_without_media_data()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => null,
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('media_data', $validator->errors()->toArray());
    }

    public function test_validation_fails_for_image_campaign_with_empty_media_data()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => json_encode([]),
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('media_data', $validator->errors()->toArray());
    }

    public function test_validation_fails_for_image_campaign_with_invalid_json()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => 'invalid-json',
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('media_data', $validator->errors()->toArray());
    }

    public function test_validation_fails_for_image_campaign_without_base64()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => json_encode([
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 1024
                // Missing base64
            ]),
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('media_data', $validator->errors()->toArray());
    }

    public function test_validation_fails_for_image_campaign_with_invalid_base64_format()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test caption',
            'media_type' => 'image',
            'media_data' => json_encode([
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'base64' => 'invalid-base64-format',
                'size' => 1024
            ]),
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('media_data', $validator->errors()->toArray());
    }

    public function test_validation_fails_for_document_campaign_without_name()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test caption',
            'media_type' => 'document',
            'media_data' => json_encode([
                'type' => 'application/pdf',
                'base64' => 'data:application/pdf;base64,JVBERi0xLjQKJcfsj6IK',
                'size' => 512
                // Missing name
            ]),
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('media_data', $validator->errors()->toArray());
    }

    public function test_validation_fails_for_text_campaign_without_message()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => '',
            'media_type' => 'text',
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('message', $validator->errors()->toArray());
    }

    public function test_validation_passes_for_valid_document_campaign()
    {
        $data = [
            'name' => 'Test Campaign',
            'message' => 'Test caption',
            'media_type' => 'document',
            'media_data' => json_encode([
                'name' => 'test.pdf',
                'type' => 'application/pdf',
                'base64' => 'data:application/pdf;base64,JVBERi0xLjQKJcfsj6IK',
                'size' => 512
            ]),
            'wuzapi_participants' => ['group1']
        ];

        $request = new MassSendingRequest();
        $request->replace($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertFalse($validator->fails());
    }
}
