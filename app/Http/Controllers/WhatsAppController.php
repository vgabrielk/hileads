<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsAppConnection;
use App\Models\WhatsAppGroup;
use App\Services\WuzapiService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    /**
     * Get WuzapiService instance with user's token
     */
    protected function getWuzapiService(): WuzapiService
    {
        // Usa o token único do usuário logado
        $userToken = auth()->user()->api_token;
        
        if (!$userToken) {
            throw new \Exception('Usuário não possui token de API. Por favor, acesse seu perfil para gerar um token.');
        }
        
        return new WuzapiService($userToken);
    }

    public function index()
    {
        $user = auth()->user();
        
        // Cache das conexões por 2 minutos
        $connections = \Cache::remember("whatsapp_connections_user_{$user->id}", 120, function () use ($user) {
            return $user->whatsappConnections()
                ->select(['id', 'phone_number', 'status', 'created_at', 'last_sync'])
                ->latest()
                ->get();
        });

        // Verificar se há conexões ativas no banco
        $activeConnections = $connections->where('status', 'active');
        
        if ($activeConnections->count() > 0) {
            $status = [
                'success' => true,
                'message' => 'Conexões WhatsApp encontradas.',
                'data' => [
                    'Connected' => true,
                    'LoggedIn' => true
                ]
            ];
        } else {
            // Se não há conexões ativas, mostrar mensagem para conectar
            $status = [
                'success' => false,
                'message' => 'Nenhuma conexão ativa. Clique em "Conectar WhatsApp" para iniciar.'
            ];
        }

        return view('whatsapp.index', compact('connections', 'status'));
    }

    public function create()
    {
        return view('whatsapp.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);

        $instanceId = 'instance_' . Str::random(10);

        try {
            // Connect to WhatsApp via Wuzapi
            $wuzapiService = $this->getWuzapiService();
            $result = $wuzapiService->connectToWhatsApp();
            $qrCode = $result['qr_code'] ?? null;

            if ($result['success'] ?? false) {
                $connection = auth()->user()->whatsappConnections()->create([
                    'phone_number' => $request->phone_number,
                    'instance_id' => $instanceId,
                    'status' => 'disconnected',
                    'connection_data' => $result,
                ]);
                return redirect()->route('whatsapp.show', $connection)
                    ->with('success', 'Conexão criada com sucesso! Escaneie o QR Code para conectar.');
            } else {
                return back()->with('error', 'Erro ao criar conexão: ' . ($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao conectar com WhatsApp: ' . $e->getMessage());
        }
    }

    public function show(WhatsAppConnection $whatsapp)
    {
        $this->authorize('view', $whatsapp);

        try {
            $wuzapiService = $this->getWuzapiService();
            $result = $wuzapiService->connectToWhatsApp();
            $qrCode = $result['qr_code'] ?? null;

            if (!$qrCode) {
                return back()->with('error', 'Não foi possível gerar o QR Code');
            }

            return view('whatsapp.show', compact('whatsapp', 'qrCode'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar QR Code: ' . $e->getMessage());
        }
    }

    public function syncGroups(WhatsAppConnection $whatsapp)
    {
        $this->authorize('view', $whatsapp);

        try {
            // Get contacts from WhatsApp
            $wuzapiService = $this->getWuzapiService();
            $contacts = $wuzapiService->getContacts();

            if ($contacts['success'] ?? false) {
                // For now, we'll create a dummy group since Wuzapi doesn't have direct group listing
                // In a real implementation, you would need to parse contacts to find groups
                $whatsapp->whatsappGroups()->updateOrCreate(
                    ['group_id' => 'general_contacts'],
                    [
                        'user_id' => auth()->id(),
                        'group_name' => 'Contatos Gerais',
                        'group_description' => 'Contatos extraídos do WhatsApp',
                        'participants_count' => count($contacts['data'] ?? []),
                    ]
                );

                $whatsapp->update(['last_sync' => now()]);

                return back()->with('success', 'Contatos sincronizados com sucesso!');
            } else {
                return back()->with('error', 'Erro ao sincronizar contatos: ' . ($contacts['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao sincronizar contatos: ' . $e->getMessage());
        }
    }

    /**
     * Sync contacts from Wuzapi into a generic local group for the user.
     */
    public function syncContacts()
    {
        try {
            $wuzapiService = $this->getWuzapiService();
            $contacts = $wuzapiService->getContacts();

            if (!($contacts['success'] ?? false)) {
                return back()->with('error', 'Erro ao sincronizar contatos: ' . ($contacts['message'] ?? 'Erro desconhecido'));
            }

            // Get or create a default connection for the user
            $user = auth()->user();
            $connection = $user->whatsappConnections()->first();
            if (!$connection) {
                $connection = WhatsAppConnection::create([
                    'user_id' => $user->id,
                    'phone_number' => 'Minha Conta',
                    'instance_id' => 'user_' . $user->id . '_default',
                    'status' => 'connected',
                    'connection_data' => null,
                ]);
            }

            // Ensure a catch-all group exists
            $group = $connection->whatsappGroups()->updateOrCreate(
                ['group_id' => 'general_contacts'],
                [
                    'user_id' => $user->id,
                    'group_name' => 'Contatos Gerais',
                    'group_description' => 'Contatos extraídos do WhatsApp',
                    'participants_count' => count($contacts['data'] ?? []),
                ]
            );

            // Map returned structure jid => info
            $extractedCount = 0;
            foreach (($contacts['data'] ?? []) as $jid => $info) {
                $phone = is_string($jid) ? explode('@', $jid)[0] : null;
                $phone = $phone ? preg_replace('/\D+/', '', $phone) : null;
                if (!$phone) { continue; }
                $name = $info['PushName'] ?? ($info['FullName'] ?? ($info['FirstName'] ?? null));

                $contact = $group->extractedContacts()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'phone_number' => $phone,
                    ],
                    [
                        'contact_name' => $name,
                        'contact_picture' => null,
                    ]
                );
                if ($contact->wasRecentlyCreated) { $extractedCount++; }
            }

            $connection->update(['last_sync' => now()]);

            return redirect()->route('contacts.index')->with('success', "Contatos sincronizados! Novos: {$extractedCount}");
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao sincronizar contatos: ' . $e->getMessage());
        }
    }

    public function destroy(WhatsAppConnection $whatsapp)
    {
        $this->authorize('delete', $whatsapp);

        try {
            $wuzapiService = $this->getWuzapiService();
            $wuzapiService->disconnectFromWhatsApp();
            $whatsapp->delete();

            return redirect()->route('whatsapp.index')
                ->with('success', 'Conexão removida com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao remover conexão: ' . $e->getMessage());
        }
    }
    public function connect()
    {
        try {
            $wuzapiService = $this->getWuzapiService();
            
            // Sempre desconectar primeiro para garantir uma nova conexão
            $disconnectResult = $wuzapiService->disconnectFromWhatsApp();
            Log::info('Desconectando sessão existente antes de conectar', [
                'disconnect_result' => $disconnectResult
            ]);
            
            // Aguardar um momento para garantir que a desconexão foi processada
            sleep(2);
            
            // Agora conectar e gerar novo QR code
            $result = $wuzapiService->connectToWhatsApp();

            if (!$result['success']) {
                return back()->with('error', 'Erro ao gerar QR code: ' . ($result['message'] ?? 'Erro desconhecido'));
            }

            // Check if qr_code exists in the result
            $qrCode = $result['qr_code'] ?? null;

            if (!$qrCode) {
                return back()->with('error', 'Não foi possível gerar o QR code. Tente novamente.');
            }

            return view('whatsapp.connect', [
                'qrCode' => $qrCode
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao conectar WhatsApp: ' . $e->getMessage());
            return back()->with('error', 'Erro ao conectar: ' . $e->getMessage());
        }
    }

    /**
     * Verifica o status de login do WhatsApp
     */
    public function checkStatus()
    {
        try {
            $wuzapiService = $this->getWuzapiService();
            $result = $wuzapiService->checkLoginStatus();

            // Se o usuário está logado, salvar a conexão no banco de dados
            if ($result['success'] && $result['logged_in']) {
                $this->saveWhatsAppConnection($wuzapiService);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar status: ' . $e->getMessage(),
                'connected' => false,
                'logged_in' => false
            ]);
        }
    }

    /**
     * Salva a conexão WhatsApp no banco de dados
     */
    private function saveWhatsAppConnection($wuzapiService)
    {
        try {
            $user = auth()->user();
            
            // Verificar se já existe uma conexão ativa para este usuário
            $existingConnection = $user->whatsappConnections()
                ->where('status', 'active')
                ->first();

            if ($existingConnection) {
                // Atualizar conexão existente
                $existingConnection->update([
                    'last_sync' => now(),
                    'status' => 'active',
                    'connection_data' => [
                        'last_verified_at' => now()->toISOString(),
                        'user_agent' => request()->userAgent(),
                        'ip_address' => request()->ip(),
                        'api_token' => substr($user->api_token, 0, 10) . '...'
                    ]
                ]);
                Log::info('Conexão WhatsApp atualizada', ['connection_id' => $existingConnection->id]);
            } else {
                // Criar nova conexão
                $connection = $user->whatsappConnections()->create([
                    'phone_number' => 'WhatsApp Connected',
                    'instance_id' => 'wuzapi_' . $user->id,
                    'status' => 'active',
                    'last_sync' => now(),
                    'connection_data' => [
                        'connected_at' => now()->toISOString(),
                        'user_agent' => request()->userAgent(),
                        'ip_address' => request()->ip(),
                        'api_token' => substr($user->api_token, 0, 10) . '...'
                    ]
                ]);
                Log::info('Nova conexão WhatsApp criada', ['connection_id' => $connection->id]);
            }
            
            // Limpar cache relacionado às conexões
            \Cache::forget("whatsapp_connections_user_{$user->id}");
            $user->clearUserCaches();
            
        } catch (\Exception $e) {
            Log::error('Erro ao salvar conexão WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Desconecta do WhatsApp (mantém sessão)
     */
    public function disconnect()
    {
        try {
            $wuzapiService = $this->getWuzapiService();
            $result = $wuzapiService->disconnectFromWhatsApp();

            if ($result['success'] ?? false) {
                return back()->with('success', 'WhatsApp desconectado com sucesso! A sessão foi mantida.');
            } else {
                return back()->with('error', 'Erro ao desconectar: ' . ($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao desconectar: ' . $e->getMessage());
        }
    }

    /**
     * Faz logout do WhatsApp (termina sessão)
     */
    public function logout()
    {
        try {
            $wuzapiService = $this->getWuzapiService();
            $result = $wuzapiService->logoutFromWhatsApp();

            if ($result['success'] ?? false) {
                return back()->with('success', 'Logout realizado com sucesso! Será necessário escanear o QR Code novamente.');
            } else {
                return back()->with('error', 'Erro ao fazer logout: ' . ($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao fazer logout: ' . $e->getMessage());
        }
    }

    /**
     * Conecta à sessão do WhatsApp (POST /session/connect)
     */
    public function connectSession()
    {
        try {
            $wuzapiService = $this->getWuzapiService();
            
            // Sempre desconectar primeiro para garantir uma nova conexão
            $disconnectResult = $wuzapiService->disconnectFromWhatsApp();
            Log::info('Desconectando sessão existente antes de conectar', [
                'disconnect_result' => $disconnectResult
            ]);
            
            // Aguardar um momento para garantir que a desconexão foi processada
            sleep(2);
            
            // Conectar ao WhatsApp
            $result = $wuzapiService->connectToWhatsApp();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conectado ao WhatsApp com sucesso!',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Erro ao conectar ao WhatsApp'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao conectar sessão WhatsApp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtém o QR Code (GET /session/qr)
     */
    public function getQR()
    {
        try {
            $wuzapiService = $this->getWuzapiService();
            $result = $wuzapiService->getQrCode();

            if ($result['success'] ?? false) {
                return response()->json([
                    'success' => true,
                    'message' => 'QR Code obtido com sucesso!',
                    'data' => $result['data'] ?? []
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Erro ao obter QR Code'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao obter QR Code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter QR Code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica o status da conexão (GET /session/status)
     */
    public function getStatus()
    {
        try {
            $wuzapiService = $this->getWuzapiService();
            $result = $wuzapiService->getStatus();

            if ($result['success']) {
                // Se o usuário está logado, salvar a conexão no banco de dados
                if ($result['data']['LoggedIn'] ?? false) {
                    $this->saveWhatsAppConnection($wuzapiService);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Status obtido com sucesso!',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Erro ao obter status'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe o fluxo de conexão
     */
    public function showConnectFlow()
    {
        return view('whatsapp.connect-flow');
    }
}
