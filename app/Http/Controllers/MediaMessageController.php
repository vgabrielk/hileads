<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WuzapiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MediaMessageController extends Controller
{
    protected function service()
    {
        return new WuzapiService(auth()->user()->api_token);
    }

    /**
     * Envia mensagem de texto
     */
    public function sendText(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'body' => 'required|string',
            'id' => 'nullable|string',
        ]);

        try {
            $service = $this->service();
            $result = $service->sendTextMessage(
                $request->phone,
                $request->body,
                $request->id ?? 'msg_' . uniqid()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Media message controller error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Envia imagem
     */
    public function sendImage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'image' => 'required|string',
            'caption' => 'nullable|string',
            'id' => 'nullable|string',
        ]);

        try {
            $service = $this->service();
            $result = $service->sendImageMessage(
                $request->phone,
                $request->image,
                $request->caption ?? '',
                $request->id ?? 'img_' . uniqid()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Media message controller error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Envia áudio
     */
    public function sendAudio(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'audio' => 'required|string',
            'id' => 'nullable|string',
        ]);

        try {
            $service = $this->service();
            $result = $service->sendAudioMessage(
                $request->phone,
                $request->audio,
                $request->id ?? 'aud_' . uniqid()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Media message controller error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Envia documento
     */
    public function sendDocument(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'document' => 'required|string',
            'fileName' => 'required|string',
            'id' => 'nullable|string',
        ]);

        try {
            $service = $this->service();
            $result = $service->sendDocumentMessage(
                $request->phone,
                $request->document,
                $request->fileName,
                $request->id ?? 'doc_' . uniqid()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Media message controller error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Envia vídeo
     */
    public function sendVideo(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'video' => 'required|string',
            'caption' => 'nullable|string',
            'id' => 'nullable|string',
        ]);

        try {
            $service = $this->service();
            $result = $service->sendVideoMessage(
                $request->phone,
                $request->video,
                $request->caption ?? '',
                $request->id ?? 'vid_' . uniqid()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Media message controller error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Envio em lote
     */
    public function sendBatch(Request $request)
    {
        $request->validate([
            'contacts' => 'required|array|min:1',
            'contacts.*' => 'required|string',
            'type' => 'required|string|in:text,image,audio,document,video',
            'data' => 'required|array',
        ]);

        try {
            $service = $this->service();
            $result = $service->sendBatchMessage(
                $request->contacts,
                $request->type,
                $request->data,
                $request->id ?? 'batch_' . uniqid()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Media message controller error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Converte arquivo para Base64
     */
    public function convertToBase64(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:50000', // 50MB max
        ]);

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getPathname());
            $base64 = base64_encode($content);
            $mimeType = $file->getMimeType();
            
            $dataUrl = 'data:' . $mimeType . ';base64,' . $base64;

            return response()->json([
                'success' => true,
                'data' => [
                    'base64' => $dataUrl,
                    'mimeType' => $mimeType,
                    'fileName' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $this->getFileType($mimeType),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Base64 conversion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao converter arquivo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detecta o tipo de arquivo baseado no MIME type
     */
    private function getFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } else {
            return 'document';
        }
    }
}
