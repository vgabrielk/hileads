<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\WuzapiService;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role
        ]);

        // Generate API token automatically
        $token = $user->generateApiToken();

        // Criar usuário na Wuzapi com o mesmo token
        try {
            $wuzapiService = new WuzapiService($token);
            $wuzapiResult = $wuzapiService->createWuzapiUser($user->name, $token);
            
            if (!$wuzapiResult['success']) {
                \Log::warning('Falha ao criar usuário na Wuzapi durante registro', [
                    'user' => $user->email,
                    'error' => $wuzapiResult['message']
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao criar usuário na Wuzapi', [
                'user' => $user->email,
                'error' => $e->getMessage()
            ]);
        }

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}