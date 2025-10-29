<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ExtractedContact;
use App\Services\WuzapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Exibe a página principal do chat.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Verificar se o usuário tem conexão WhatsApp ativa
        // Status pode ser 'active' ou 'connected'
        $hasActiveConnection = $user->whatsappConnections()
            ->whereIn('status', ['active', 'connected'])
            ->exists();
        
        if (!$hasActiveConnection) {
            return redirect()->route('whatsapp.index')
                ->with('error', 'Você precisa conectar uma conta WhatsApp primeiro para usar o chat.');
        }

        return view('chat.index', [
            'user' => $user,
        ]);
    }

    /**
     * Obtém a lista de conversas do usuário.
     */
    public function getConversations(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Buscar conversas do banco de dados (ordenadas por última mensagem)
            $conversations = ChatConversation::query()
                ->forUser($user->id)
                ->active()
                ->latestMessage()
                ->get()
                ->map(function ($conversation) {
                    return [
                        'id' => $conversation->id,
                        'chat_jid' => $conversation->chat_jid,
                        'contact_name' => $conversation->contact_name,
                        'contact_phone' => $conversation->contact_phone,
                        'display_name' => $conversation->display_name,
                        'formatted_phone' => $conversation->formatted_phone,
                        'last_message_text' => $conversation->last_message_text,
                        'last_message_preview' => $conversation->last_message_preview,
                        'last_message_time' => $conversation->last_message_time,
                        'last_message_from_me' => $conversation->last_message_from_me,
                        'unread_count' => $conversation->unread_count,
                        'avatar_url' => $conversation->avatar_url,
                    ];
                });

            // Se não houver conversas no banco, tentar sincronizar contatos extraídos
            if ($conversations->isEmpty()) {
                $this->syncConversationsFromContacts($user);
                
                // Recarregar conversas
                $conversations = ChatConversation::query()
                    ->forUser($user->id)
                    ->active()
                    ->latestMessage()
                    ->get()
                    ->map(function ($conversation) {
                        return [
                            'id' => $conversation->id,
                            'chat_jid' => $conversation->chat_jid,
                            'contact_name' => $conversation->contact_name,
                            'contact_phone' => $conversation->contact_phone,
                            'display_name' => $conversation->display_name,
                            'formatted_phone' => $conversation->formatted_phone,
                            'last_message_text' => $conversation->last_message_text,
                            'last_message_preview' => $conversation->last_message_preview,
                            'last_message_time' => $conversation->last_message_time,
                            'last_message_from_me' => $conversation->last_message_from_me,
                            'unread_count' => $conversation->unread_count,
                            'avatar_url' => $conversation->avatar_url,
                        ];
                    });
            }

            return response()->json([
                'success' => true,
                'data' => $conversations,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting conversations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar conversas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sincroniza conversas a partir dos contatos extraídos.
     */
    private function syncConversationsFromContacts($user)
    {
        try {
            $contacts = ExtractedContact::where('user_id', $user->id)
                ->whereNotNull('phone_number')
                ->get();

            foreach ($contacts as $contact) {
                $phone = preg_replace('/[^0-9]/', '', $contact->phone_number);
                $chatJid = $phone . '@s.whatsapp.net';

                ChatConversation::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'chat_jid' => $chatJid,
                    ],
                    [
                        'extracted_contact_id' => $contact->id,
                        'contact_name' => $contact->contact_name,
                        'contact_phone' => $phone,
                        'is_active' => true,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Error syncing conversations: ' . $e->getMessage());
        }
    }

    /**
     * Obtém o histórico de mensagens de uma conversa.
     */
    public function getMessages(Request $request, $conversationId)
    {
        try {
            $user = auth()->user();
            $conversation = ChatConversation::where('user_id', $user->id)
                ->where('id', $conversationId)
                ->firstOrFail();

            // Buscar histórico da API Wuzapi
            $wuzapiService = new WuzapiService($user->api_token);
            $limit = $request->input('limit', 50);
            
            $response = $wuzapiService->getChatHistory($conversation->chat_jid, $limit);

            if (!$response['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $response['message'] ?? 'Erro ao carregar mensagens',
                ], 500);
            }

            $messages = $this->formatMessages($response['data']);

            // Marcar conversa como lida
            $conversation->markAsRead();

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation' => [
                        'id' => $conversation->id,
                        'chat_jid' => $conversation->chat_jid,
                        'contact_name' => $conversation->contact_name,
                        'display_name' => $conversation->display_name,
                        'avatar_url' => $conversation->avatar_url,
                    ],
                    'messages' => $messages,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar mensagens: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Formata mensagens da API para o formato esperado pelo frontend.
     */
    private function formatMessages($apiMessages)
    {
        if (empty($apiMessages) || !is_array($apiMessages)) {
            return [];
        }

        return collect($apiMessages)->map(function ($msg) {
            $messageType = 'text';
            $mediaLink = null;
            
            // Detectar tipo de mensagem
            if (isset($msg['ImageMessage'])) {
                $messageType = 'image';
                $mediaLink = $msg['ImageMessage']['URL'] ?? null;
            } elseif (isset($msg['VideoMessage'])) {
                $messageType = 'video';
                $mediaLink = $msg['VideoMessage']['URL'] ?? null;
            } elseif (isset($msg['AudioMessage'])) {
                $messageType = 'audio';
                $mediaLink = $msg['AudioMessage']['URL'] ?? null;
            } elseif (isset($msg['DocumentMessage'])) {
                $messageType = 'document';
                $mediaLink = $msg['DocumentMessage']['URL'] ?? null;
            } elseif (isset($msg['StickerMessage'])) {
                $messageType = 'sticker';
                $mediaLink = $msg['StickerMessage']['URL'] ?? null;
            }

            return [
                'id' => $msg['Info']['ID'] ?? uniqid(),
                'timestamp' => $msg['Info']['Timestamp'] ?? time(),
                'from_me' => $msg['Info']['FromMe'] ?? false,
                'sender_jid' => $msg['Info']['Sender'] ?? null,
                'text' => $msg['Message']['Conversation'] ?? $msg['Message']['ExtendedTextMessage']['Text'] ?? '',
                'message_type' => $messageType,
                'media_link' => $mediaLink,
                'status' => $msg['Info']['Status'] ?? 'sent',
            ];
        })->values()->toArray();
    }

    /**
     * Envia uma mensagem de texto.
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:4096',
        ]);

        try {
            $user = auth()->user();
            $conversation = ChatConversation::where('user_id', $user->id)
                ->where('id', $conversationId)
                ->firstOrFail();

            $wuzapiService = new WuzapiService($user->api_token);
            
            // Extrair telefone do JID
            $phone = str_replace('@s.whatsapp.net', '', $conversation->chat_jid);
            
            // Enviar mensagem
            $response = $wuzapiService->sendTextMessage($phone, $request->message);

            if (!$response['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $response['message'] ?? 'Erro ao enviar mensagem',
                ], 500);
            }

            // Atualizar última mensagem da conversa
            $conversation->updateLastMessage($request->message, true);

            return response()->json([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso',
                'data' => [
                    'message_id' => $response['data']['MessageID'] ?? null,
                    'timestamp' => now()->timestamp,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Envia uma mensagem com mídia.
     */
    public function sendMedia(Request $request, $conversationId)
    {
        $request->validate([
            'media_type' => 'required|in:image,video,audio,document,sticker',
            'media_file' => 'required|file|max:16384', // 16MB
            'caption' => 'nullable|string|max:1024',
        ]);

        try {
            $user = auth()->user();
            $conversation = ChatConversation::where('user_id', $user->id)
                ->where('id', $conversationId)
                ->firstOrFail();

            // Converter arquivo para base64
            $file = $request->file('media_file');
            $mimeType = $file->getMimeType();
            $fileContent = file_get_contents($file->getRealPath());
            $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);

            $wuzapiService = new WuzapiService($user->api_token);
            $phone = str_replace('@s.whatsapp.net', '', $conversation->chat_jid);
            
            // Enviar de acordo com o tipo
            $response = null;
            switch ($request->media_type) {
                case 'image':
                    $response = $wuzapiService->sendImageMessage($phone, $base64, $request->caption ?? '');
                    break;
                case 'video':
                    $response = $wuzapiService->sendVideoMessage($phone, $base64, $request->caption ?? '');
                    break;
                case 'audio':
                    $response = $wuzapiService->sendAudioMessage($phone, $base64);
                    break;
                case 'document':
                    $response = $wuzapiService->sendDocumentMessage($phone, $base64, $file->getClientOriginalName());
                    break;
                case 'sticker':
                    $response = $wuzapiService->sendStickerMessage($phone, $base64);
                    break;
            }

            if (!$response || !$response['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $response['message'] ?? 'Erro ao enviar mídia',
                ], 500);
            }

            // Atualizar última mensagem
            $lastMessageText = $request->caption ?: '[' . ucfirst($request->media_type) . ']';
            $conversation->updateLastMessage($lastMessageText, true);

            return response()->json([
                'success' => true,
                'message' => 'Mídia enviada com sucesso',
                'data' => [
                    'message_id' => $response['data']['MessageID'] ?? null,
                    'timestamp' => now()->timestamp,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending media: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mídia: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marca mensagens como lidas.
     */
    public function markAsRead(Request $request, $conversationId)
    {
        try {
            $user = auth()->user();
            $conversation = ChatConversation::where('user_id', $user->id)
                ->where('id', $conversationId)
                ->firstOrFail();

            $conversation->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Mensagens marcadas como lidas',
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como lido: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verifica se há novas mensagens (para long polling).
     */
    public function checkNewMessages(Request $request)
    {
        try {
            $user = auth()->user();
            $lastCheck = $request->input('last_check');
            
            // Buscar conversas atualizadas desde o último check
            $query = ChatConversation::query()
                ->forUser($user->id)
                ->active();
            
            if ($lastCheck) {
                $query->where('updated_at', '>', $lastCheck);
            }
            
            $updatedConversations = $query->latestMessage()
                ->get()
                ->map(function ($conversation) {
                    return [
                        'id' => $conversation->id,
                        'chat_jid' => $conversation->chat_jid,
                        'last_message_text' => $conversation->last_message_text,
                        'last_message_preview' => $conversation->last_message_preview,
                        'last_message_time' => $conversation->last_message_time,
                        'last_message_from_me' => $conversation->last_message_from_me,
                        'unread_count' => $conversation->unread_count,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'has_updates' => $updatedConversations->isNotEmpty(),
                    'conversations' => $updatedConversations,
                    'timestamp' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking new messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar novas mensagens: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Inicia ou busca uma conversa existente a partir de um telefone.
     * Usado para iniciar chat a partir da lista de contatos.
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'nullable|string',
            'contact_id' => 'nullable|integer|exists:extracted_contacts,id',
        ]);

        try {
            $user = auth()->user();
            
            // Verificar se usuário tem conexão WhatsApp ativa
            $hasActiveConnection = $user->whatsappConnections()
                ->whereIn('status', ['active', 'connected'])
                ->exists();
            
            if (!$hasActiveConnection) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você precisa conectar uma conta WhatsApp primeiro.',
                    'redirect' => route('whatsapp.index'),
                ], 403);
            }

            // Limpar telefone e criar JID
            $phone = preg_replace('/[^0-9]/', '', $request->phone);
            $chatJid = $phone . '@s.whatsapp.net';

            // Buscar ou criar conversa
            $conversation = ChatConversation::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'chat_jid' => $chatJid,
                ],
                [
                    'extracted_contact_id' => $request->contact_id,
                    'contact_name' => $request->name,
                    'contact_phone' => $phone,
                    'is_active' => true,
                ]
            );

            // Se a conversa já existia mas estava inativa, reativá-la
            if (!$conversation->is_active) {
                $conversation->update(['is_active' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversa iniciada com sucesso',
                'data' => [
                    'conversation_id' => $conversation->id,
                    'chat_jid' => $conversation->chat_jid,
                    'display_name' => $conversation->display_name,
                ],
                'redirect' => route('chat.index') . '?conversation=' . $conversation->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting conversation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar conversa: ' . $e->getMessage(),
            ], 500);
        }
    }
}

