<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MassSending;
use App\Models\ExtractedContact;
use App\Services\WuzapiService;
use App\Services\DiscordLoggerService;
use App\Jobs\ProcessMassSendingJob;
use App\Http\Requests\MassSendingRequest;
use App\Helpers\CampaignLogger;
use App\Helpers\JsonSanitizer;

class MassSendingController extends Controller
{
    private function service(): WuzapiService
    {
        return new WuzapiService(auth()->user()->api_token);
    }
    
    private function discordLogger(): DiscordLoggerService
    {
        return new DiscordLoggerService();
    }

    public function index()
    {
        // Use a more efficient query with explicit ordering and limits
        $massSendings = auth()->user()->massSendings()
            ->select('id', 'name', 'message', 'message_type', 'status', 'total_contacts', 'sent_count', 'delivered_count', 'failed_count', 'created_at', 'started_at', 'completed_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('mass-sendings.index', compact('massSendings'));
    }

    public function create(Request $request)
    {
        try {
            // Log de entrada no método
            \Log::info('🚀 Iniciando método create do MassSendingController', [
                'user_id' => auth()->id(),
                'request_url' => request()->url(),
                'request_method' => request()->method(),
                'has_group_id' => $request->has('group_id')
            ]);
            
            // Enviar log de entrada para Discord
            try {
                $this->discordLogger()->logSuccess(
                    '🚀 Mass Sendings Create - Início',
                    'Método create iniciado com sucesso',
                    [
                        'user_id' => auth()->id(),
                        'request_url' => request()->url(),
                        'has_group_id' => $request->has('group_id')
                    ]
                );
            } catch (\Exception $e) {
                \Log::error('Erro ao enviar log de entrada para Discord: ' . $e->getMessage());
            }
            // Check if this is a group-based mass sending
            $group = null;
            if ($request->has('group_id')) {
                \Log::info('🔍 Processando grupo específico', ['group_id' => $request->group_id]);
                $group = \App\Models\Group::findOrFail($request->group_id);
                $this->authorize('view', $group);
                \Log::info('✅ Grupo autorizado com sucesso');
            }

        // Get WhatsApp groups in real-time from Wuzapi API
        $wuzapiGroups = collect();
        $apiError = false;
        $apiErrorMessage = '';
        $connectionIssue = false;
        $needsConnection = false;
        $needsLogin = false;
        
        try {
            // Primeiro, verificar se a ligação WhatsApp está ativa
            \Log::info('🔍 Verificando estado da ligação WhatsApp');
            $connectionCheck = $this->service()->checkConnectionBeforeGroups();
            
            if (!$connectionCheck['success']) {
                $connectionIssue = true;
                $apiError = true;
                $apiErrorMessage = $connectionCheck['message'];
                $needsConnection = $connectionCheck['needs_connection'] ?? false;
                $needsLogin = $connectionCheck['needs_login'] ?? false;
                
                \Log::warning('❌ Problema de ligação WhatsApp: ' . $apiErrorMessage);
            } else {
                \Log::info('✅ Conexão WhatsApp verificada, buscando grupos...');
                $response = $this->service()->getGroups();
                
                \Log::info('📡 Resposta da API getGroups:', [
                    'success' => $response['success'] ?? false,
                    'data_count' => count($response['data'] ?? []),
                    'message' => $response['message'] ?? null
                ]);
                
                if ($response['success'] ?? false) {
                    $wuzapiGroups = collect($response['data'] ?? [])->map(function($group) {
                        try {
                            // Validar se o grupo tem dados mínimos necessários
                            if (!is_array($group) || empty($group)) {
                                \Log::warning('Grupo inválido ignorado: dados vazios ou não array');
                                return null;
                            }
                            
                            // Sanitizar nome do grupo para evitar problemas
                            $groupName = $this->sanitizeGroupName($group['Name'] ?? 'Grupo');
                            
                            // Get all participant JIDs (including @lid)
                            $participantJIDs = collect($group['Participants'] ?? [])
                                ->pluck('JID')
                                ->filter()
                                ->values()
                                ->all();
                            
                            // Generate group avatar com tratamento de erro
                            $groupPhoto = $this->generateGroupAvatar($groupName);
                            
                            return [
                                'jid' => $group['JID'] ?? '',
                                'name' => $groupName,
                                'participants_count' => count($group['Participants'] ?? []),
                                'participant_jids' => $participantJIDs,
                                'participants' => $group['Participants'] ?? [],
                                'is_announce' => $group['IsAnnounce'] ?? false,
                                'created_at' => $group['GroupCreated'] ?? null,
                                'photo' => $groupPhoto,
                                'from_api' => true,
                            ];
                        } catch (\Exception $e) {
                            $errorContext = [
                                'group_data' => $group,
                                'error' => $e->getMessage(),
                                'user_id' => auth()->id()
                            ];
                            
                            \Log::error('Erro ao processar grupo da API:', $errorContext);
                            
                            // Enviar para Discord
                            $this->discordLogger()->logError(
                                '📊 Erro ao Processar Grupo da API',
                                "Erro ao processar grupo da API: {$e->getMessage()}",
                                $errorContext
                            );
                            
                            return null;
                        }
                    })->filter(); // Remove grupos nulos
                    
                    \Log::info('✅ Grupos obtidos com sucesso:', [
                        'total_groups' => $wuzapiGroups->count()
                    ]);
                } else {
                    $apiError = true;
                    $apiErrorMessage = $response['message'] ?? 'Erro desconhecido na API';
                    \Log::warning('❌ Falha ao obter grupos da API Wuzapi: ' . $apiErrorMessage);
                }
            }
        } catch (\Exception $e) {
            $errorContext = [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => auth()->id(),
                'request_url' => request()->url(),
                'request_method' => request()->method()
            ];
            
            \Log::error('💥 Erro ao procurar grupos da API Wuzapi: ' . $e->getMessage(), $errorContext);
            
            // Enviar para Discord
            $this->discordLogger()->logError(
                '🚨 Erro na API Wuzapi - Mass Sendings Create',
                "Erro ao procurar grupos da API Wuzapi: {$e->getMessage()}",
                $errorContext
            );
            
            $apiError = true;
            $apiErrorMessage = $e->getMessage();
            
            // Adicionar grupos de exemplo para demonstração quando a API falha
            $wuzapiGroups = collect([
                [
                    'JID' => 'example_group_1@g.us',
                    'Name' => 'Família Silva',
                    'Participants' => [
                        ['JID' => '5511999999999@s.whatsapp.net', 'PhoneNumber' => '5511999999999@s.whatsapp.net'],
                        ['JID' => '5511888888888@s.whatsapp.net', 'PhoneNumber' => '5511888888888@s.whatsapp.net'],
                        ['JID' => '5511777777777@s.whatsapp.net', 'PhoneNumber' => '5511777777777@s.whatsapp.net'],
                    ],
                    'IsAnnounce' => false,
                    'GroupCreated' => now(),
                ],
                [
                    'JID' => 'example_group_2@g.us',
                    'Name' => 'Trabalho - Equipe Vendas',
                    'Participants' => [
                        ['JID' => '5511666666666@s.whatsapp.net', 'PhoneNumber' => '5511666666666@s.whatsapp.net'],
                        ['JID' => '5511555555555@s.whatsapp.net', 'PhoneNumber' => '5511555555555@s.whatsapp.net'],
                    ],
                    'IsAnnounce' => false,
                    'GroupCreated' => now(),
                ],
                [
                    'JID' => 'example_group_3@g.us',
                    'Name' => 'Amigos da Faculdade',
                    'Participants' => [
                        ['JID' => '5511444444444@s.whatsapp.net', 'PhoneNumber' => '5511444444444@s.whatsapp.net'],
                        ['JID' => '5511333333333@s.whatsapp.net', 'PhoneNumber' => '5511333333333@s.whatsapp.net'],
                        ['JID' => '5511222222222@s.whatsapp.net', 'PhoneNumber' => '5511222222222@s.whatsapp.net'],
                    ],
                    'IsAnnounce' => false,
                    'GroupCreated' => now(),
                ]
            ])->map(function($group) {
                $participantJIDs = collect($group['Participants'] ?? [])
                    ->pluck('JID')
                    ->filter()
                    ->values()
                    ->all();
                
                $groupPhoto = $this->generateGroupAvatar($group['Name'] ?? 'Grupo');
                
                return [
                    'jid' => $group['JID'] ?? '',
                    'name' => $group['Name'] ?? 'Grupo sem nome',
                    'participants_count' => count($group['Participants'] ?? []),
                    'participant_jids' => $participantJIDs,
                    'participants' => $group['Participants'] ?? [],
                    'is_announce' => $group['IsAnnounce'] ?? false,
                    'created_at' => $group['GroupCreated'] ?? null,
                    'photo' => $groupPhoto,
                    'from_api' => false,
                    'is_example' => true,
                ];
            });
        }
        
        // Filtrar apenas grupos com participantes válidos (telefones reais)
        $validGroups = [];
        foreach ($wuzapiGroups as $group) {
            $validParticipants = [];
            foreach ($group['participants'] as $participant) {
                // Verificar se é um array e tem PhoneNumber
                if (is_array($participant) && isset($participant['PhoneNumber'])) {
                    $phoneNumber = $participant['PhoneNumber'];
                    // Aceitar apenas telefones que terminam com @s.whatsapp.net
                    if (str_ends_with($phoneNumber, '@s.whatsapp.net')) {
                        $validParticipants[] = $phoneNumber;
                    }
                }
            }
            
            if (!empty($validParticipants)) {
                $group['valid_participants'] = $validParticipants;
                $group['valid_count'] = count($validParticipants);
                $validGroups[] = $group;
            }
        }
        
        \Log::info('📊 Processamento de grupos concluído:', [
            'total_groups_from_api' => $wuzapiGroups->count(),
            'valid_groups_with_participants' => count($validGroups),
            'api_error' => $apiError,
            'api_error_message' => $apiErrorMessage,
            'connection_issue' => $connectionIssue,
            'needs_connection' => $needsConnection,
            'needs_login' => $needsLogin
        ]);
            
        return view('mass-sendings.create', compact('validGroups', 'apiError', 'apiErrorMessage', 'connectionIssue', 'needsConnection', 'needsLogin', 'group'));
        
        } catch (\Exception $e) {
            $errorContext = [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_url' => request()->url(),
                'request_method' => request()->method()
            ];
            
            \Log::error('💥 Erro crítico no método create:', $errorContext);
            
            // Enviar para Discord
            $this->discordLogger()->logError(
                '🚨 Erro Crítico - Mass Sendings Create',
                "Erro crítico no método create: {$e->getMessage()}",
                $errorContext
            );
            
            // Retornar view com erro genérico para evitar 500
            return view('mass-sendings.create', [
                'validGroups' => [],
                'apiError' => true,
                'apiErrorMessage' => 'Erro interno do servidor. Tente novamente em alguns minutos.',
                'connectionIssue' => true,
                'needsConnection' => false,
                'needsLogin' => false,
                'group' => null
            ]);
        } catch (\Throwable $e) {
            // Catch global para qualquer erro, incluindo erros fatais
            $errorContext = [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_url' => request()->url(),
                'request_method' => request()->method(),
                'error_type' => get_class($e)
            ];
            
            \Log::error('💥 ERRO FATAL no método create:', $errorContext);
            
            // Enviar para Discord
            try {
                $this->discordLogger()->logError(
                    '💥 ERRO FATAL - Mass Sendings Create',
                    "ERRO FATAL no método create: {$e->getMessage()}",
                    $errorContext
                );
            } catch (\Exception $discordError) {
                \Log::error('Erro ao enviar para Discord: ' . $discordError->getMessage());
            }
            
            // Retornar view com erro genérico para evitar 500
            return view('mass-sendings.create', [
                'validGroups' => [],
                'apiError' => true,
                'apiErrorMessage' => 'Erro interno do servidor. Tente novamente em alguns minutos.',
                'connectionIssue' => true,
                'needsConnection' => false,
                'needsLogin' => false,
                'group' => null
            ]);
        }
    }

    /**
     * Regenera o token de API do utilizador
     */
    public function regenerateToken()
    {
        try {
            $user = auth()->user();
            $newToken = 'wuzapi_' . bin2hex(random_bytes(32));
            
            $user->update(['api_token' => $newToken]);
            
            return response()->json([
                'success' => true,
                'message' => 'Token regenerado com sucesso! Reconecte o WhatsApp com o novo token.',
                'new_token' => $newToken
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao regenerar token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao regenerar token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Inicia religação do WhatsApp
     */
    public function reconnectWhatsApp()
    {
        try {
            $response = $this->service()->connectToWhatsApp();
            
            return response()->json([
                'success' => $response['success'] ?? false,
                'message' => $response['message'] ?? 'Tentativa de religação iniciada',
                'qr_code' => $response['qr_code'] ?? null,
                'already_connected' => $response['already_connected'] ?? false,
                'already_logged_in' => $response['already_logged_in'] ?? false
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao reconectar WhatsApp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reconectar WhatsApp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sanitiza o nome do grupo para evitar problemas com caracteres especiais
     */
    private function sanitizeGroupName(string $groupName): string
    {
        try {
            // Se o nome já está vazio ou é apenas "Grupo", retornar como está
            if (empty(trim($groupName)) || trim($groupName) === 'Grupo') {
                return 'Grupo sem nome';
            }
            
            // Remover caracteres de controle e caracteres especiais problemáticos
            $cleanName = preg_replace('/[\x00-\x1F\x7F-\x9F]/', '', $groupName);
            
            // Manter caracteres Unicode comuns (incluindo acentos e emojis básicos)
            $cleanName = preg_replace('/[^\x20-\x7E\xC0-\xFF\x{0100}-\x{017F}\x{0180}-\x{024F}\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $cleanName);
            
            // Limitar tamanho
            $cleanName = mb_substr(trim($cleanName), 0, 50);
            
            // Se ficou vazio após limpeza, usar nome padrão
            if (empty($cleanName)) {
                $cleanName = 'Grupo sem nome';
            }
            
            return $cleanName;
        } catch (\Exception $e) {
            \Log::warning('Erro ao sanitizar nome do grupo: ' . $e->getMessage(), [
                'original_name' => $groupName,
                'error' => $e->getMessage()
            ]);
            return 'Grupo sem nome';
        }
    }

    /**
     * Gera um avatar baseado no nome do grupo
     */
    private function generateGroupAvatar(string $groupName): string
    {
        try {
            // Cores disponíveis para os avatares
            $colors = [
                'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500',
                'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500',
                'bg-orange-500', 'bg-cyan-500', 'bg-lime-500', 'bg-amber-500'
            ];
            
            // Limpar e sanitizar o nome do grupo
            $cleanName = trim($groupName);
            if (empty($cleanName)) {
                $cleanName = 'Grupo';
            }
            
            // Limitar o tamanho do nome para evitar problemas
            $cleanName = substr($cleanName, 0, 50);
            
            // Pega as primeiras letras do nome do grupo
            $initials = '';
            $words = explode(' ', $cleanName);
            foreach ($words as $word) {
                if (!empty($word)) {
                    // Remover caracteres especiais e pegar apenas letras
                    $cleanWord = preg_replace('/[^a-zA-Z0-9]/', '', $word);
                    if (!empty($cleanWord)) {
                        $initials .= strtoupper(substr($cleanWord, 0, 1));
                        if (strlen($initials) >= 2) break;
                    }
                }
            }
            
            // Se não conseguir pegar iniciais, usa as primeiras letras do nome
            if (empty($initials)) {
                $cleanName = preg_replace('/[^a-zA-Z0-9]/', '', $cleanName);
                $initials = strtoupper(substr($cleanName, 0, 2));
                if (empty($initials)) {
                    $initials = 'G';
                }
            }
            
            // Seleciona uma cor baseada no hash do nome
            $colorIndex = abs(crc32($cleanName)) % count($colors);
            $color = $colors[$colorIndex];
            
            // Escapar caracteres especiais no SVG
            $escapedInitials = htmlspecialchars($initials, ENT_QUOTES, 'UTF-8');
            
            $svg = "<svg width='56' height='56' viewBox='0 0 56 56' xmlns='http://www.w3.org/2000/svg'>
                <rect width='56' height='56' rx='12' fill='currentColor' class='{$color}'/>
                <text x='28' y='32' text-anchor='middle' fill='white' font-family='system-ui, sans-serif' font-size='16' font-weight='bold'>{$escapedInitials}</text>
            </svg>";
            
            return "data:image/svg+xml;base64," . base64_encode($svg);
            
        } catch (\Exception $e) {
            $errorContext = [
                'group_name' => $groupName,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ];
            
            \Log::error('Erro ao gerar avatar do grupo: ' . $e->getMessage(), $errorContext);
            
            // Enviar para Discord
            $this->discordLogger()->logError(
                '🎨 Erro ao Gerar Avatar do Grupo',
                "Erro ao gerar avatar do grupo: {$e->getMessage()}",
                $errorContext
            );
            
            // Retornar avatar padrão em caso de erro
            return "data:image/svg+xml;base64," . base64_encode("
                <svg width='56' height='56' viewBox='0 0 56 56' xmlns='http://www.w3.org/2000/svg'>
                    <rect width='56' height='56' rx='12' fill='currentColor' class='bg-blue-500'/>
                    <text x='28' y='32' text-anchor='middle' fill='white' font-family='system-ui, sans-serif' font-size='16' font-weight='bold'>G</text>
                </svg>
            ");
        }
    }

    public function store(MassSendingRequest $request)
    {
        CampaignLogger::startProcess('MassSendingController@store', [
            'user_id' => auth()->id(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'has_media_type' => $request->has('media_type'),
            'has_media_data' => $request->has('media_data')
        ]);

        // Verificar limite de campanhas do plano
        $user = auth()->user();
        CampaignLogger::info('Verificando limites do plano', [
            'user_id' => $user->id,
            'plan_id' => $user->subscription?->plan_id
        ]);

        $campaignCheck = \App\Helpers\PlanLimitsHelper::canCreateCampaign($user);
        
        if (!$campaignCheck['can_create']) {
            CampaignLogger::error('Limite de campanhas excedido', [
                'message' => $campaignCheck['message']
            ]);
            return back()->withErrors([
                'name' => $campaignCheck['message']
            ]);
        }

        CampaignLogger::info('Dados da requisição recebidos', [
            'has_media_type' => $request->has('media_type'),
            'has_media_data' => $request->has('media_data'),
            'media_type' => $request->input('media_type'),
            'message_length' => strlen($request->input('message') ?? ''),
            'media_caption_length' => strlen($request->input('media_caption') ?? ''),
            'has_group_id' => $request->has('group_id'),
            'group_id' => $request->input('group_id'),
            'has_wuzapi_participants' => $request->has('wuzapi_participants'),
            'wuzapi_participants_count' => count($request->input('wuzapi_participants', [])),
            'has_manual_numbers' => $request->has('manual_numbers'),
            'manual_numbers_length' => strlen($request->input('manual_numbers') ?? '')
        ]);

        // Log dados de mídia se presente
        if ($request->has('media_data')) {
            $mediaData = $request->input('media_data');
            
            // Decodificar JSON com sanitização
            $decodedMediaData = null;
            if (is_string($mediaData)) {
                $decodedMediaData = JsonSanitizer::decode($mediaData, true);
                
                if ($decodedMediaData === null) {
                    CampaignLogger::error('JSON inválido no controller após sanitização', [
                        'media_data_length' => strlen($mediaData),
                        'error_info' => JsonSanitizer::getErrorInfo($mediaData)
                    ]);
                }
            } else {
                $decodedMediaData = $mediaData;
            }
            
            CampaignLogger::mediaData('Dados de mídia recebidos no controller', $decodedMediaData ?? []);
        }

        $wuzapiParticipants = $request->wuzapi_participants ?? [];
        $manualNumbers = $request->manual_numbers ?? '';
        $groupParticipants = [];
        
        // Processar participantes de grupo se especificado
        if ($request->has('group_id')) {
            $group = \App\Models\Group::findOrFail($request->group_id);
            $this->authorize('view', $group);
            
            $groupContacts = $group->getContactsFromApi();
            foreach ($groupContacts as $contact) {
                $groupParticipants[] = $contact['jid'];
            }
        }
        
        // Processar números manuais
        $manualParticipants = [];
        if (!empty($manualNumbers)) {
            $numbers = array_filter(array_map('trim', explode("\n", $manualNumbers)));
            foreach ($numbers as $number) {
                if (!empty($number) && is_numeric($number)) {
                    // Adicionar @s.whatsapp.net para formar o JID
                    $manualParticipants[] = $number . '@s.whatsapp.net';
                }
            }
        }
        
        // Combinar participantes de grupos, grupos personalizados e números manuais
        $allParticipants = array_merge($wuzapiParticipants, $groupParticipants, $manualParticipants);
        $totalContacts = count($allParticipants);
        
        // Validar se há pelo menos um destinatário
        if ($totalContacts === 0) {
            return back()->withErrors([
                'wuzapi_participants' => 'Selecione pelo menos um grupo ou adicione números manualmente.'
            ]);
        }

        // Validar se há conteúdo (texto ou mídia)
        if (empty($request->message) && !$request->has('media_data')) {
            \Log::warning('❌ No content provided', [
                'has_message' => !empty($request->message),
                'has_media_data' => $request->has('media_data'),
                'message' => $request->message,
                'media_type' => $request->input('media_type')
            ]);
            return back()->withErrors([
                'message' => 'Digite uma mensagem ou adicione uma mídia.'
            ]);
        }

        // Determinar o tipo de mensagem e conteúdo
        $messageType = 'text';
        $messageContent = $request->message;
        $mediaData = null;
        
        // Se há mídia selecionada (vem do JavaScript)
        if ($request->has('media_type') && $request->has('media_data')) {
            CampaignLogger::info('Processando dados de mídia', [
                'media_type' => $request->media_type,
                'has_media_caption' => $request->has('media_caption'),
                'media_caption_length' => strlen($request->media_caption ?? '')
            ]);

            $messageType = $request->media_type;
            $messageContent = $request->media_caption ?? '';
            
            // Decodificar dados de mídia com sanitização
            $mediaData = null;
            if (is_string($request->media_data)) {
                $mediaData = JsonSanitizer::decode($request->media_data, true);
                
                if ($mediaData === null) {
                    CampaignLogger::error('JSON inválido no processamento de mídia após sanitização', [
                        'media_data_length' => strlen($request->media_data),
                        'error_info' => JsonSanitizer::getErrorInfo($request->media_data)
                    ]);
                }
            } else {
                $mediaData = $request->media_data;
            }
                
            CampaignLogger::mediaData('Dados de mídia processados', $mediaData ?? []);
                
            \Log::info('📱 Media mass sending created', [
                'message_type' => $messageType,
                'message_content' => $messageContent,
                'media_data_keys' => $mediaData ? array_keys($mediaData) : null,
                'has_base64' => isset($mediaData['base64']) ? !empty($mediaData['base64']) : false,
                'media_data_full' => $mediaData,
                'base64_length' => isset($mediaData['base64']) ? strlen($mediaData['base64']) : 0
            ]);
        }

        CampaignLogger::database('Criando MassSending no banco de dados', [
            'name' => $request->name,
            'message_type' => $messageType,
            'message_content_length' => strlen($messageContent),
            'has_media_data' => !empty($mediaData),
            'media_data_type' => gettype($mediaData),
            'total_contacts' => $totalContacts,
            'participants_count' => count($allParticipants),
            'scheduled_at' => $request->scheduled_at
        ]);

        $massSending = auth()->user()->massSendings()->create([
            'name' => $request->name,
            'message' => $messageContent,
            'message_type' => $messageType,
            'media_data' => $mediaData,
            'status' => 'draft',
            'contact_ids' => [],
            'wuzapi_participants' => $allParticipants,
            'total_contacts' => $totalContacts,
            'scheduled_at' => $request->scheduled_at,
        ]);

        CampaignLogger::database('MassSending criado com sucesso', [
            'mass_sending_id' => $massSending->id,
            'message_type' => $massSending->message_type,
            'has_media_data' => !empty($massSending->media_data),
            'media_data_type' => gettype($massSending->media_data),
            'media_data_keys' => is_array($massSending->media_data) ? array_keys($massSending->media_data) : 'not_array',
            'status' => $massSending->status,
            'total_contacts' => $massSending->total_contacts
        ]);

        // Debug: Log after creation to verify data was saved
        \Log::info('💾 Mass sending created in database', [
            'mass_sending_id' => $massSending->id,
            'message_type' => $massSending->message_type,
            'media_data_saved' => $massSending->media_data,
            'media_data_raw' => $massSending->getRawOriginal('media_data'),
            'has_media_data' => !empty($massSending->media_data),
            'media_data_keys' => is_array($massSending->media_data) ? array_keys($massSending->media_data) : 'not_array'
        ]);

        // Mass sending is created as draft - user needs to start it manually

        $message = "Envio em massa criado com sucesso! {$totalContacts} destinatários adicionados.";
        if (count($wuzapiParticipants) > 0 && count($manualParticipants) > 0) {
            $groupCount = $totalContacts - count($manualParticipants);
            $manualCount = count($manualParticipants);
            $message .= " ({$groupCount} de grupos + {$manualCount} manuais)";
        } elseif (count($manualParticipants) > 0) {
            $message .= " (" . count($manualParticipants) . " números manuais)";
        }

        CampaignLogger::endProcess('MassSendingController@store', [
            'mass_sending_id' => $massSending->id,
            'message_type' => $massSending->message_type,
            'total_contacts' => $totalContacts,
            'status' => 'draft',
            'success_message' => $message
        ]);

        return redirect()->route('mass-sendings.show', $massSending)
            ->with('success', $message);
    }

    public function show(MassSending $massSending)
    {
        $this->authorize('view', $massSending);
        
        // Cache dos participantes por 10 minutos para evitar múltiplas chamadas à API
        $cacheKey = "mass_sending_participants_{$massSending->id}";
        $wuzapiParticipants = \Cache::remember($cacheKey, 600, function () use ($massSending) {
            $participants = [];
            if (!empty($massSending->wuzapi_participants)) {
                try {
                    $jids = $massSending->wuzapi_participants;
                    
                    // Get all contacts from Wuzapi API (same as ContactController)
                    $contactsData = [];
                    try {
                        $contactsResponse = $this->service()->getContacts();
                        if ($contactsResponse['success'] ?? false) {
                            $contactsData = $contactsResponse['data'] ?? [];
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Erro ao obter contactos da Wuzapi: ' . $e->getMessage());
                    }
                    
                    // Map JIDs for display with contact info
                    $participants = collect($jids)->map(function($jid) use ($contactsData) {
                        // Extract phone number from JID
                        $phone = str_replace('@s.whatsapp.net', '', $jid);
                        
                        // Find contact info for this JID
                        $contactInfo = $contactsData[$jid] ?? null;
                        
                        // Get name from contact info (same logic as ContactController)
                        $name = null;
                        if ($contactInfo) {
                            $name = $contactInfo['PushName'] ?? null;
                        }
                        
                        return [
                            'jid' => $jid,
                            'phone' => $phone,
                            'name' => $name,
                            'type' => 'Telefone WhatsApp'
                        ];
                    })->values()->all();
                    
                } catch (\Exception $e) {
                    \Log::error('Erro ao processar participantes Wuzapi: ' . $e->getMessage());
                }
            }
            return $participants;
        });
            
        return view('mass-sendings.show', compact('massSending', 'wuzapiParticipants'));
    }

    public function start(MassSending $massSending)
    {
        $this->authorize('update', $massSending);
        
        if ($massSending->status !== 'draft') {
            return back()->with('error', 'Apenas envio em massas em rascunho podem ser iniciadas.');
        }

        $massSending->update([
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Dispatch job to process mass sending
        ProcessMassSendingJob::dispatch($massSending);

        return back()->with('success', 'Envio em massa iniciada! O envio está sendo processado em segundo plano.');
    }

    public function pause(MassSending $massSending)
    {
        $this->authorize('update', $massSending);
        
        // Set pause flag in cache for immediate effect
        $pauseKey = "mass_sending_pause_{$massSending->id}";
        \Cache::put($pauseKey, true, 3600); // 1 hour
        
        // Update database status
        $massSending->update(['status' => 'paused']);
        
        \Log::info("⏸️ Mass sending paused", [
            'mass_sending_id' => $massSending->id,
            'pause_key' => $pauseKey
        ]);
        
        return back()->with('success', 'Envio em massa pausado com sucesso!');
    }

    public function resume(MassSending $massSending)
    {
        $this->authorize('update', $massSending);
        
        // Clear pause flag
        $pauseKey = "mass_sending_pause_{$massSending->id}";
        \Cache::forget($pauseKey);
        
        // Check if there are remaining contacts to send
        $remainingContacts = $massSending->total_contacts - $massSending->sent_count;
        
        if ($remainingContacts <= 0) {
            return back()->with('error', 'Não há contactos restantes para enviar!');
        }
        
        // Update status to active
        $massSending->update(['status' => 'active']);
        
        // Dispatch job to process remaining contacts
        ProcessMassSendingJob::dispatch($massSending);
        
        \Log::info("▶️ Mass sending resumed", [
            'mass_sending_id' => $massSending->id,
            'remaining_contacts' => $remainingContacts
        ]);
        
        return back()->with('success', "Envio em massa retomada! Processando {$remainingContacts} contactos restantes...");
    }
    
    public function progress(MassSending $massSending)
    {
        $this->authorize('view', $massSending);
        
        $progressKey = "mass_sending_progress_{$massSending->id}";
        $progress = \Cache::get($progressKey, [
            'status' => 'not_started',
            'total' => 0,
            'sent' => 0,
            'failed' => 0,
            'current_message' => 'Envio em massa não iniciada',
            'started_at' => null,
            'completed_at' => null,
        ]);
        
        // If campaign is completed or failed, clean up cache after some time
        if (in_array($progress['status'], ['completed', 'failed']) && isset($progress['completed_at'])) {
            $completedAt = \Carbon\Carbon::parse($progress['completed_at']);
            if ($completedAt->diffInMinutes(now()) > 5) {
                \Cache::forget($progressKey);
                $progress['status'] = 'not_started';
            }
        }
        
        return response()->json($progress);
    }

    public function destroy(MassSending $massSending)
    {
        $this->authorize('delete', $massSending);
        
        if ($massSending->status === 'active') {
            return back()->with('error', 'Não é possível eliminar uma envio em massa ativa.');
        }
        
        $massSending->delete();
        
        return redirect()->route('mass-sendings.index')
            ->with('success', 'Envio em massa eliminada com sucesso!');
    }

    /**
     * Update mass sending inline (AJAX)
     */
    public function updateInline(Request $request, MassSending $massSending)
    {
        $this->authorize('update', $massSending);

        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Não permitir editar envio em massas ativas
        if ($massSending->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível editar uma envio em massa ativa.'
            ], 422);
        }

        $massSending->update([
            'name' => $request->name,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Envio em massa atualizada com sucesso!',
            'campaign' => [
                'id' => $massSending->id,
                'name' => $massSending->name,
                'message' => $massSending->message,
                'status' => $massSending->status,
            ]
        ]);
    }

    /**
     * Resend mass sending with new message
     */
    public function resend(Request $request, MassSending $massSending)
    {
        $this->authorize('update', $massSending);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Criar nova envio em massa baseada na atual
        $newCampaign = MassSending::create([
            'user_id' => $massSending->user_id,
            'name' => $massSending->name . ' (Reenvio)',
            'message' => $request->message,
            'status' => 'draft',
            'contact_ids' => $massSending->contact_ids,
            'wuzapi_participants' => $massSending->wuzapi_participants,
            'total_contacts' => $massSending->total_contacts,
            'sent_count' => 0,
            'delivered_count' => 0,
            'read_count' => 0,
            'replied_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nova envio em massa criada com sucesso!',
            'campaign' => [
                'id' => $newCampaign->id,
                'name' => $newCampaign->name,
                'message' => $newCampaign->message,
                'status' => $newCampaign->status,
            ],
            'redirect_url' => route('mass-sendings.show', $newCampaign)
        ]);
    }

    /**
     * Get mass sending data for inline editing
     */
    public function getEditData(MassSending $massSending)
    {
        $this->authorize('view', $massSending);

        return response()->json([
            'success' => true,
            'campaign' => [
                'id' => $massSending->id,
                'name' => $massSending->name,
                'message' => $massSending->message,
                'status' => $massSending->status,
                'can_edit' => $massSending->status !== 'active',
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MassSending $massSending)
    {
        $this->authorize('view', $massSending);

        // Não permitir editar envio em massas ativas
        if ($massSending->status === 'active') {
            return redirect()->route('mass-sendings.index')
                ->with('error', 'Não é possível editar uma envio em massa ativa.');
        }

        // Get WhatsApp groups in real-time from Wuzapi API
        $wuzapiGroups = [];
        $apiError = false;
        
        try {
            $response = $this->service()->getGroups();
            if ($response['success'] ?? false) {
                $wuzapiGroups = collect($response['data'] ?? [])->map(function($group) {
                    // Get all participant JIDs (including @lid)
                    $participantJIDs = collect($group['Participants'] ?? [])
                        ->pluck('JID')
                        ->filter()
                        ->values()
                        ->all();
                    
                    // Generate group avatar
                    $groupPhoto = $this->generateGroupAvatar($group['Name'] ?? 'Grupo');
                    
                    return [
                        'jid' => $group['JID'] ?? '',
                        'name' => $group['Name'] ?? 'Grupo sem nome',
                        'participants_count' => count($participantJIDs),
                        'participant_jids' => $participantJIDs,
                        'participants' => collect($group['Participants'] ?? [])->map(function($participant) {
                            return [
                                'JID' => $participant['JID'] ?? '',
                                'PhoneNumber' => $participant['PhoneNumber'] ?? $participant['JID'] ?? '',
                            ];
                        })->toArray(),
                        'is_announce' => $group['IsAnnounce'] ?? false,
                        'created_at' => $group['CreatedAt'] ?? now(),
                        'photo' => $groupPhoto,
                        'from_database' => false,
                    ];
                });
            } else {
                $apiError = true;
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching groups for mass sending edit: ' . $e->getMessage());
            $apiError = true;
        }
        
        // Filter only groups with valid participants (real phone numbers)
        $validGroups = [];
        foreach ($wuzapiGroups as $wuzapiGroup) {
            $validParticipants = [];
            foreach ($wuzapiGroup['participants'] as $participant) {
                // Check if it's an array and has PhoneNumber
                if (is_array($participant) && isset($participant['PhoneNumber'])) {
                    $phoneNumber = $participant['PhoneNumber'];
                    // Accept only phones that end with @s.whatsapp.net
                    if (str_ends_with($phoneNumber, '@s.whatsapp.net')) {
                        $validParticipants[] = $phoneNumber;
                    }
                }
            }
            
            if (!empty($validParticipants)) {
                $wuzapiGroup['valid_participants'] = $validParticipants;
                $wuzapiGroup['valid_count'] = count($validParticipants);
                $validGroups[] = $wuzapiGroup;
            }
        }
        
        return view('mass-sendings.edit', compact('massSending', 'validGroups', 'apiError'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MassSending $massSending)
    {
        $this->authorize('update', $massSending);

        // Não permitir editar envio em massas ativas
        if ($massSending->status === 'active') {
            return redirect()->route('mass-sendings.index')
                ->with('error', 'Não é possível editar uma envio em massa ativa.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'nullable|string|max:4096',
            'media_caption' => 'nullable|string|max:1000',
            'media_type' => 'nullable|string|in:text,image,video,audio,document',
            'media_data' => 'nullable|string',
            'wuzapi_participants' => 'nullable|array',
            'wuzapi_participants.*' => 'string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        try {
            // Get participants from form
            $wuzapiParticipants = $request->wuzapi_participants ?? [];
            $totalContacts = count($wuzapiParticipants);
            
            // Validate if there's at least one recipient
            if ($totalContacts === 0) {
                return back()->withErrors([
                    'wuzapi_participants' => 'Selecione pelo menos um grupo ou adicione números manualmente.'
                ]);
            }

            // Determine message type and content
            $messageType = 'text';
            $messageContent = $request->message;
            $mediaData = null;
            
            // If there's media selected (comes from JavaScript)
            if ($request->has('media_type') && $request->has('media_data')) {
                $messageType = $request->media_type;
                $messageContent = $request->media_caption ?? '';
                
                // Decode media data if it comes as JSON string
                $mediaData = is_string($request->media_data) 
                    ? json_decode($request->media_data, true) 
                    : $request->media_data;
            }

            // Update mass sending
            $massSending->update([
                'name' => $request->name,
                'message' => $messageContent,
                'message_type' => $messageType,
                'media_data' => $mediaData,
                'wuzapi_participants' => $wuzapiParticipants,
                'total_contacts' => $totalContacts,
                'scheduled_at' => $request->scheduled_at,
            ]);

            return redirect()->route('mass-sendings.index')
                ->with('success', 'Envio em massa atualizado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Error updating mass sending: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar envio em massa. Tente novamente.');
        }
    }
}
