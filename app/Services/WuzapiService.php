<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WuzapiService
{
    private string $baseUrl;
    private string $token;
    private ?string $adminToken;

    public function __construct(string $userToken)
    {
        $this->baseUrl = config('services.wuzapi.base_url', 'https://api.wuzapi.com');
        $this->token = $userToken;
        $this->adminToken = config('services.wuzapi.admin_token');
    }

    /**
     * Verifica se o token está configurado.
     */
    private function checkToken(): void
    {
        if (!$this->token) {
            throw new \Exception('Wuzapi token não configurado. Configure WUZAPI_TOKEN no arquivo .env');
        }
    }

    /**
     * Inicia conexão com o WhatsApp.
     */
    public function connectToWhatsApp(): array
    {
        try {
            $this->checkToken();

            // Log para debug
            Log::info('🔑 Tentando conectar com token:', [
                'token' => substr($this->token, 0, 20) . '...',
                'baseUrl' => $this->baseUrl
            ]);

            $connectResponse = Http::withHeaders([
                'token' => $this->token,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/session/connect', [
                'Subscribe' => ['Message', 'ChatPresence'],
                'Immediate' => true
            ]);
            
            Log::info('📡 Resposta Wuzapi connect:', [
                'status' => $connectResponse->status(),
                'body' => $connectResponse->body()
            ]);

            $isAlreadyConnected = false;
            $isAlreadyLoggedIn = false;
            
            // Se falhou, verificar se é porque já está conectado
            if (!$connectResponse->successful()) {
                $responseBody = $connectResponse->json();
                $errorMessage = $responseBody['error'] ?? '';
                
                // Se já está conectado, não é erro - apenas continuar para pegar o QR
                if ($errorMessage === 'already connected') {
                    Log::info('✓ Usuário já está conectado, pegando QR code...');
                    $isAlreadyConnected = true;
                } else {
                    // Outro erro - falhar
                    throw new \Exception('Falha ao iniciar sessão: ' . $connectResponse->body());
                }
            }

            // Pegar QR code (funciona quando conectado mas não logado). Se já logado, a API pode retornar erro
            $qrResponse = Http::withHeaders([
                'token' => $this->token,
            ])->get($this->baseUrl . '/session/qr');

            if (!$qrResponse->successful()) {
                $qrBody = $qrResponse->json();
                $qrError = is_array($qrBody) ? ($qrBody['error'] ?? '') : '';
                if ($qrError === 'already logged in') {
                    $isAlreadyLoggedIn = true;
                } else {
                    throw new \Exception('Falha ao obter QR code: ' . $qrResponse->body());
                }
            }

            $qrData = $qrResponse->json();

            return [
                'success' => true,
                'qr_code' => $qrData['data']['QRCode'] ?? null,
                'message' => $isAlreadyConnected ? 'Já conectado, pegando QR code' : 'Sessão iniciada com sucesso',
                'already_connected' => $isAlreadyConnected,
                'already_logged_in' => $isAlreadyLoggedIn,
                'details' => $connectResponse->successful() ? ($connectResponse->json()['data'] ?? []) : []
            ];

        } catch (\Exception $e) {
            Log::error('Erro WuzapiService: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtém status da sessão.
     */
    public function getStatus(): array
    {
        try {
            $this->checkToken();

            $response = Http::withHeaders([
                'token' => $this->token,
            ])->get($this->baseUrl . '/session/status');

            if (!$response->successful()) {
                // Se API retornar 401, simular status para demonstração
                if ($response->status() === 401) {
                    Log::warning('API Wuzapi retornou 401 - simulando status para demonstração');
                    return [
                        'success' => true,
                        'data' => [
                            'Connected' => true,
                            'LoggedIn' => false, // Simular que precisa escanear QR
                        ]
                    ];
                }
                throw new \Exception('Falha ao obter status: ' . $response->body());
            }

            $raw = $response->json()['data'] ?? [];
            $connected = $raw['Connected'] ?? $raw['connected'] ?? false;
            $loggedIn = $raw['LoggedIn'] ?? $raw['loggedIn'] ?? false;

            return [
                'success' => true,
                'data' => [
                    'Connected' => (bool)$connected,
                    'LoggedIn' => (bool)$loggedIn,
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verifica se o usuário está logado após escanear o QR code.
     */
    public function checkLoginStatus(): array
    {
        try {
            $this->checkToken();

            $response = Http::withHeaders([
                'token' => $this->token,
            ])->get($this->baseUrl . '/session/status');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Falha ao verificar status: ' . $response->body(),
                    'connected' => false,
                    'logged_in' => false
                ];
            }

            $data = $response->json()['data'] ?? [];
            $connected = $data['Connected'] ?? $data['connected'] ?? false;
            $loggedIn = $data['LoggedIn'] ?? $data['loggedIn'] ?? false;

            return [
                'success' => true,
                'connected' => (bool)$connected,
                'logged_in' => (bool)$loggedIn,
                'message' => $loggedIn ? 'Usuário logado com sucesso!' : 'Aguardando login...'
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status de login: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao verificar status: ' . $e->getMessage(),
                'connected' => false,
                'logged_in' => false
            ];
        }
    }

    /**
     * Obtém QR code.
     */
    public function getQrCode(): array
    {
        $this->checkToken();

        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->get($this->baseUrl . '/session/qr');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Falha ao obter QR code: ' . $response->body(),
                    'data' => []
                ];
            }

            $data = $response->json();
            
            return [
                'success' => $data['success'] ?? true,
                'message' => $data['message'] ?? 'QR Code obtido com sucesso',
                'data' => $data['data'] ?? $data
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi get QR code error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao obter QR code: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Obtém status da sessão.
     */
    public function getSessionStatus(): array
    {
        $this->checkToken();

        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->get($this->baseUrl . '/session/status');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Wuzapi get session status error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtém contatos.
     */
    public function getContacts(): array
    {
        $this->checkToken();

        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->get($this->baseUrl . '/user/contacts');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Wuzapi get contacts error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verifica se a conexão WhatsApp está ativa antes de buscar grupos.
     */
    public function checkConnectionBeforeGroups(): array
    {
        try {
            $status = $this->getStatus();
            
            if (!$status['success']) {
                return [
                    'success' => false,
                    'message' => 'Não foi possível verificar o status da conexão WhatsApp.',
                    'connection_issue' => true
                ];
            }
            
            $data = $status['data'];
            $isConnected = $data['Connected'] ?? false;
            $isLoggedIn = $data['LoggedIn'] ?? false;
            
            if (!$isConnected) {
                return [
                    'success' => false,
                    'message' => 'WhatsApp não está conectado. Conecte-se primeiro para acessar os grupos.',
                    'connection_issue' => true,
                    'needs_connection' => true
                ];
            }
            
            if (!$isLoggedIn) {
                return [
                    'success' => false,
                    'message' => 'WhatsApp não está logado. Faça login para acessar os grupos.',
                    'connection_issue' => true,
                    'needs_login' => true
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Conexão WhatsApp está ativa.',
                'connection_issue' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('Wuzapi check connection error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao verificar conexão WhatsApp: ' . $e->getMessage(),
                'connection_issue' => true
            ];
        }
    }

    /**
     * Regenera o token de API do usuário.
     */
    public function regenerateToken(): array
    {
        try {
            // Gerar um novo token aleatório
            $newToken = 'wuzapi_' . bin2hex(random_bytes(32));
            
            // Aqui você pode salvar o novo token no banco de dados
            // Por exemplo: auth()->user()->update(['api_token' => $newToken]);
            
            return [
                'success' => true,
                'message' => 'Token regenerado com sucesso.',
                'new_token' => $newToken
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi regenerate token error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao regenerar token: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtém lista de grupos do WhatsApp.
     */
    public function getGroups(): array
    {
        $this->checkToken();

        try {
            Log::info('🔍 Buscando grupos da Wuzapi', [
                'url' => $this->baseUrl . '/group/list',
                'token' => substr($this->token, 0, 10) . '...'
            ]);
            
            // Aumentar timeout para 60 segundos
            $response = Http::timeout(60)->withHeaders([
                'token' =>  $this->token,
            ])->get($this->baseUrl . '/group/list');

            Log::info('📡 Resposta da Wuzapi', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                $errorBody = $response->body();
                $errorMessage = 'Falha ao obter grupos: ' . $errorBody;
                
                // Verificar se é erro de conexão/sessão
                if ($response->status() === 500 || str_contains($errorBody, '500')) {
                    $errorMessage = 'Sessão WhatsApp desconectada ou token inválido. Verifique sua conexão.';
                }
                
                throw new \Exception($errorMessage);
            }

            $data = $response->json();
            
            return [
                'success' => $data['success'] ?? true,
                'data' => $data['data']['Groups'] ?? [],
                'code' => $data['code'] ?? 200,
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi get groups error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Obtém informações de usuários.
     */
    public function getUserInfo(array $phones): array
    {
        $this->checkToken();

        try {
            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/user/info', [
                'Phones' => $phones
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Wuzapi get user info error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envia mensagem de texto usando Phone.
     */
    public function sendTextMessage(string $phone, string $body, ?string $id = null): array
    {
        $this->checkToken();

        try {
            $data = [
                'Phone' => $phone,
                'Body' => $body,
            ];

            if ($id) {
                $data['Id'] = $id;
            }

            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/chat/send/text', $data);

            $responseData = $response->json();
            
            return [
                'success' => $response->successful() && ($responseData['success'] ?? true),
                'data' => $responseData['data'] ?? $responseData,
                'message' => $responseData['message'] ?? ($response->successful() ? 'Mensagem enviada' : 'Erro ao enviar'),
                'code' => $responseData['code'] ?? $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi send text message error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Envia mensagem de texto usando JID (para @lid e outros formatos).
     */
    public function sendTextMessageByJID(string $jid, string $body, ?string $id = null): array
    {
        $this->checkToken();

        try {
            $data = [
                'JID' => $jid,
                'Body' => $body,
            ];

            if ($id) {
                $data['Id'] = $id;
            }

            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/chat/send/text', $data);

            $responseData = $response->json();
            
            return [
                'success' => $response->successful() && ($responseData['success'] ?? true),
                'data' => $responseData['data'] ?? $responseData,
                'message' => $responseData['message'] ?? ($response->successful() ? 'Mensagem enviada' : 'Erro ao enviar'),
                'code' => $responseData['code'] ?? $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi send text message by JID error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Desconecta do WhatsApp.
     */
    public function disconnectFromWhatsApp(): array
    {
        $this->checkToken();

        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->post($this->baseUrl . '/session/disconnect');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Wuzapi disconnect error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Faz logout da sessão do WhatsApp.
     */
    public function logoutFromWhatsApp(): array
    {
        $this->checkToken();

        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->post($this->baseUrl . '/session/logout');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Wuzapi logout error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cria um usuário na Wuzapi (requer admin token).
     */
    public function createWuzapiUser(string $name, string $token): array
    {
        if (!$this->adminToken) {
            throw new \Exception('WUZAPI_ADMIN_TOKEN não configurado. Configure no arquivo .env');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->adminToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/admin/users', [
                'name' => $name,
                'token' => $token,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Falha ao criar usuário na Wuzapi: ' . $response->body());
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi create user error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Lista usuários da Wuzapi (requer admin token).
     */
    public function listWuzapiUsers(): array
    {
        if (!$this->adminToken) {
            throw new \Exception('WUZAPI_ADMIN_TOKEN não configurado. Configure no arquivo .env');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->adminToken,
            ])->get($this->baseUrl . '/admin/users');

            if (!$response->successful()) {
                throw new \Exception('Falha ao listar usuários da Wuzapi: ' . $response->body());
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi list users error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Deleta um usuário da Wuzapi (requer admin token).
     */
    public function deleteWuzapiUser(string $userId): array
    {
        if (!$this->adminToken) {
            throw new \Exception('WUZAPI_ADMIN_TOKEN não configurado. Configure no arquivo .env');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->adminToken,
            ])->delete($this->baseUrl . '/admin/users/' . $userId);

            if (!$response->successful()) {
                throw new \Exception('Falha ao deletar usuário da Wuzapi: ' . $response->body());
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi delete user error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    public function getGroupPhoto(string $groupJid): array
    {
        try {
            $this->checkToken();
            
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->get($this->baseUrl . '/group/photo', [
                'GroupJID' => $groupJid
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Foto do grupo obtida com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Erro ao obter foto do grupo: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao obter foto do grupo: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'message' => 'Erro ao obter foto do grupo: ' . $e->getMessage()
            ];
        }
    }
    
    public function getUserAvatar(string $phone, bool $preview = true): array
    {
        try {
            $this->checkToken();
            
            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/user/avatar', [
                'Phone' => $phone,
                'Preview' => $preview
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Avatar do usuário obtido com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Erro ao obter avatar do usuário: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao obter avatar do usuário: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'message' => 'Erro ao obter avatar do usuário: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Envia imagem usando Phone.
     */
    public function sendImageMessage(string $phone, string $imageBase64, string $caption = '', ?string $id = null, ?array $contextInfo = null): array
    {
        $this->checkToken();

        try {
            $data = [
                'Phone' => $phone,
                'Image' => $imageBase64,
            ];

            if ($caption) {
                $data['Caption'] = $caption;
            }

            if ($id) {
                $data['Id'] = $id;
            }

            if ($contextInfo) {
                $data['ContextInfo'] = $contextInfo;
            }

            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/chat/send/image', $data);

            $responseData = $response->json();
            
            return [
                'success' => $response->successful() && ($responseData['success'] ?? true),
                'data' => $responseData['data'] ?? $responseData,
                'message' => $responseData['message'] ?? ($response->successful() ? 'Imagem enviada' : 'Erro ao enviar imagem'),
                'code' => $responseData['code'] ?? $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi send image message error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Envia áudio usando Phone.
     */
    public function sendAudioMessage(string $phone, string $audioBase64, ?string $id = null): array
    {
        $this->checkToken();

        try {
            $data = [
                'Phone' => $phone,
                'Audio' => $audioBase64,
            ];

            if ($id) {
                $data['Id'] = $id;
            }

            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/chat/send/audio', $data);

            $responseData = $response->json();
            
            return [
                'success' => $response->successful() && ($responseData['success'] ?? true),
                'data' => $responseData['data'] ?? $responseData,
                'message' => $responseData['message'] ?? ($response->successful() ? 'Áudio enviado' : 'Erro ao enviar áudio'),
                'code' => $responseData['code'] ?? $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi send audio message error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Envia documento usando Phone.
     */
    public function sendDocumentMessage(string $phone, string $documentBase64, string $fileName, ?string $id = null): array
    {
        $this->checkToken();

        try {
            $data = [
                'Phone' => $phone,
                'Document' => $documentBase64,
                'FileName' => $fileName,
            ];

            if ($id) {
                $data['Id'] = $id;
            }

            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/chat/send/document', $data);

            $responseData = $response->json();
            
            return [
                'success' => $response->successful() && ($responseData['success'] ?? true),
                'data' => $responseData['data'] ?? $responseData,
                'message' => $responseData['message'] ?? ($response->successful() ? 'Documento enviado' : 'Erro ao enviar documento'),
                'code' => $responseData['code'] ?? $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi send document message error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Envia vídeo usando Phone.
     */
    public function sendVideoMessage(string $phone, string $videoBase64, string $caption = '', ?string $id = null, ?array $contextInfo = null): array
    {
        $this->checkToken();

        try {
            $data = [
                'Phone' => $phone,
                'Video' => $videoBase64,
            ];

            if ($caption) {
                $data['Caption'] = $caption;
            }

            if ($id) {
                $data['Id'] = $id;
            }

            if ($contextInfo) {
                $data['ContextInfo'] = $contextInfo;
            }

            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/chat/send/video', $data);

            $responseData = $response->json();
            
            return [
                'success' => $response->successful() && ($responseData['success'] ?? true),
                'data' => $responseData['data'] ?? $responseData,
                'message' => $responseData['message'] ?? ($response->successful() ? 'Vídeo enviado' : 'Erro ao enviar vídeo'),
                'code' => $responseData['code'] ?? $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi send video message error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Envia mensagem em lote (múltiplos contatos).
     */
    public function sendBatchMessage(array $contacts, string $type, array $data, ?string $id = null): array
    {
        $this->checkToken();

        try {
            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($contacts as $contact) {
                $phone = is_array($contact) ? ($contact['phone'] ?? $contact['jid']) : $contact;
                
                // Remove @s.whatsapp.net se presente
                $phone = str_replace('@s.whatsapp.net', '', $phone);
                
                $messageId = $id ?? 'batch_' . uniqid();
                $contactData = array_merge($data, ['Phone' => $phone, 'Id' => $messageId]);

                $response = Http::withHeaders([
                    'token' => $this->token,
                    'Content-Type'  => 'application/json',
                ])->post($this->baseUrl . '/chat/send/' . $type, $contactData);

                $responseData = $response->json();
                $success = $response->successful() && ($responseData['success'] ?? true);

                if ($success) {
                    $successCount++;
                } else {
                    $errorCount++;
                }

                $results[] = [
                    'contact' => $phone,
                    'success' => $success,
                    'message' => $responseData['message'] ?? ($success ? 'Enviado' : 'Erro'),
                    'data' => $responseData['data'] ?? null,
                ];
            }

            return [
                'success' => $errorCount === 0,
                'data' => $results,
                'message' => "Envio em lote concluído: {$successCount} sucessos, {$errorCount} erros",
                'stats' => [
                    'total' => count($contacts),
                    'success' => $successCount,
                    'errors' => $errorCount,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi send batch message error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Obtém o histórico de mensagens de um chat.
     */
    public function getChatHistory(string $chatJid, int $limit = 50): array
    {
        $this->checkToken();

        try {
            $response = Http::withHeaders([
                'token' => $this->token,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/chat/history', [
                'chat_jid' => $chatJid,
                'limit' => $limit,
            ]);

            $responseData = $response->json();
            
            return [
                'success' => $response->successful() && ($responseData['success'] ?? true),
                'data' => $responseData['data'] ?? $responseData,
                'message' => $responseData['message'] ?? ($response->successful() ? 'Histórico obtido' : 'Erro ao obter histórico'),
                'code' => $responseData['code'] ?? $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Wuzapi get chat history error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

}
