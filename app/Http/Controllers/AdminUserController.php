<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\WuzapiService;
use Illuminate\Support\Facades\Http;

class AdminUserController extends Controller
{
    /**
     * List all users.
     */
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ], 200);
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string|in:user,admin',
            'token' => 'nullable|string|max:64', // Token customizado opcional
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : Hash::make('password123'),
            'role' => $request->role ?? 'user',
        ]);

        // Generate API token (use custom token if provided, otherwise generate one)
        if ($request->has('token')) {
            $user->api_token = $request->token;
            $user->save();
            $token = $request->token;
        } else {
            $token = $user->generateApiToken();
        }

        // Criar usuário na Wuzapi com o mesmo token
        try {
            $wuzapiService = new WuzapiService($token);
            $wuzapiResult = $wuzapiService->createWuzapiUser($user->name, $token);
            
            $response = [
                'success' => true,
                'message' => 'Usuário criado com sucesso.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'token' => $token,
                    'created_at' => $user->created_at,
                    'wuzapi_created' => $wuzapiResult['success']
                ]
            ];

            if (!$wuzapiResult['success']) {
                $response['warning'] = 'Usuário criado no Laravel, mas houve erro ao criar na Wuzapi: ' . $wuzapiResult['message'];
            }

            return response()->json($response, 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário criado no Laravel, mas erro ao criar na Wuzapi.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'token' => $token,
                    'created_at' => $user->created_at,
                ],
                'wuzapi_error' => $e->getMessage()
            ], 201);
        }
    }

    /**
     * Show a specific user.
     */
    public function show($id)
    {
        $user = User::select('id', 'name', 'email', 'role', 'created_at')
            ->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'sometimes|required|string|in:user,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'updated_at' => $user->updated_at
            ]
        ], 200);
    }

    /**
     * Delete a user.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        // Prevent deletion of current admin user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode deletar sua própria conta.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário deletado com sucesso.'
        ], 200);
    }

    /**
     * Regenerate API token for a user.
     */
    public function regenerateToken($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        $token = $user->generateApiToken();

        return response()->json([
            'success' => true,
            'message' => 'Token regenerado com sucesso.',
            'data' => [
                'token' => $token
            ]
        ], 200);
    }

    /**
     * List all Wuzapi users page (admin only - web).
     */
    public function wuzapiUsersPage()
    {
        // Verificar se é admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores.');
        }

        try {
            // Buscar todos os usuários do Laravel com token
            $laravelUsers = User::whereNotNull('api_token')->get();

            // Verificar status de cada usuário na Wuzapi
            $enrichedUsers = $laravelUsers->map(function ($user) {
                try {
                    // Tentar pegar status da sessão com o token do usuário
                    $response = Http::withHeaders([
                        'token' => $user->api_token,
                    ])->get(config('services.wuzapi.base_url') . '/session/status');

                    $wuzapiData = null;
                    if ($response->successful()) {
                        $responseData = $response->json();
                        $wuzapiData = [
                            'connected' => $responseData['data']['Connected'] ?? false,
                            'loggedIn' => $responseData['data']['LoggedIn'] ?? false,
                            'token' => $user->api_token,
                            'status_code' => 200
                        ];
                    } else {
                        $wuzapiData = [
                            'connected' => false,
                            'loggedIn' => false,
                            'token' => $user->api_token,
                            'status_code' => $response->status()
                        ];
                    }
                } catch (\Exception $e) {
                    $wuzapiData = [
                        'connected' => false,
                        'loggedIn' => false,
                        'token' => $user->api_token,
                        'error' => $e->getMessage()
                    ];
                }

                return [
                    'laravel' => $user,
                    'wuzapi' => $wuzapiData
                ];
            });

            return view('admin.wuzapi-users', [
                'users' => $enrichedUsers,
                'error' => null
            ]);

        } catch (\Exception $e) {
            return view('admin.wuzapi-users', [
                'error' => 'Erro ao buscar usuários: ' . $e->getMessage(),
                'users' => []
            ]);
        }
    }

    /**
     * List all Wuzapi users API (admin only - JSON).
     */
    public function listWuzapiUsers()
    {
        $adminToken = config('services.wuzapi.admin_token');
        
        if (!$adminToken) {
            return response()->json([
                'success' => false,
                'message' => 'WUZAPI_ADMIN_TOKEN não configurado.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $adminToken,
            ])->get(config('services.wuzapi.base_url') . '/admin/users');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao buscar usuários da Wuzapi: ' . $response->body()
                ], $response->status());
            }

            $wuzapiData = $response->json();

            // Mapear tokens da Wuzapi com usuários do Laravel
            $wuzapiUsers = $wuzapiData['data'] ?? [];
            $laravelUsers = User::whereNotNull('api_token')->get();

            // Enriquecer dados da Wuzapi com informações do Laravel
            $enrichedUsers = collect($wuzapiUsers)->map(function ($wuzapiUser) use ($laravelUsers) {
                $laravelUser = $laravelUsers->firstWhere('api_token', $wuzapiUser['token']);
                
                return [
                    'wuzapi' => $wuzapiUser,
                    'laravel' => $laravelUser ? [
                        'id' => $laravelUser->id,
                        'name' => $laravelUser->name,
                        'email' => $laravelUser->email,
                        'role' => $laravelUser->role,
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $enrichedUsers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com Wuzapi: ' . $e->getMessage()
            ], 500);
        }
    }
}
