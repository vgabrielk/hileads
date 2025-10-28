<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MissingMediaException extends Exception
{
    protected $message = 'Media data is required for media campaigns but was not provided or is invalid.';
    protected $code = 422;

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'error_code' => 'MISSING_MEDIA_DATA',
            'details' => [
                'mass_sending_id' => $this->getMassSendingId(),
                'message_type' => $this->getMessageType(),
                'provided_media_data' => $this->getProvidedMediaData()
            ]
        ], $this->code);
    }

    /**
     * Create a new exception instance with context data.
     */
    public static function forMassSending(int $massSendingId, string $messageType, $mediaData = null): self
    {
        $exception = new self();
        $exception->massSendingId = $massSendingId;
        $exception->messageType = $messageType;
        $exception->providedMediaData = $mediaData;
        
        return $exception;
    }

    private $massSendingId;
    private $messageType;
    private $providedMediaData;

    public function getMassSendingId(): ?int
    {
        return $this->massSendingId;
    }

    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    public function getProvidedMediaData()
    {
        return $this->providedMediaData;
    }
}
