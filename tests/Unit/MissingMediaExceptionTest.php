<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Exceptions\MissingMediaException;
use Illuminate\Http\Request;

class MissingMediaExceptionTest extends TestCase
{
    public function test_exception_has_correct_default_message()
    {
        $exception = new MissingMediaException();

        $this->assertEquals('Media data is required for media campaigns but was not provided or is invalid.', $exception->getMessage());
        $this->assertEquals(422, $exception->getCode());
    }

    public function test_exception_can_be_created_with_context()
    {
        $massSendingId = 123;
        $messageType = 'image';
        $mediaData = ['base64' => ''];

        $exception = MissingMediaException::forMassSending($massSendingId, $messageType, $mediaData);

        $this->assertEquals($massSendingId, $exception->getMassSendingId());
        $this->assertEquals($messageType, $exception->getMessageType());
        $this->assertEquals($mediaData, $exception->getProvidedMediaData());
    }

    public function test_exception_renders_json_response()
    {
        $massSendingId = 123;
        $messageType = 'image';
        $mediaData = ['base64' => ''];

        $exception = MissingMediaException::forMassSending($massSendingId, $messageType, $mediaData);
        $request = new Request();

        $response = $exception->render($request);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Media data is required for media campaigns but was not provided or is invalid.', $responseData['message']);
        $this->assertEquals('MISSING_MEDIA_DATA', $responseData['error_code']);
        $this->assertEquals($massSendingId, $responseData['details']['mass_sending_id']);
        $this->assertEquals($messageType, $responseData['details']['message_type']);
        $this->assertEquals($mediaData, $responseData['details']['provided_media_data']);
    }
}
