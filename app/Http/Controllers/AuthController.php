<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\WuzapiService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {

        // Rate limiting for login attempts
        $key = 'login_attempts_' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 15;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            Log::warning('Too many login attempts', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'seconds_remaining' => $seconds
            ]);
            
            return back()->withErrors([
                'email' => "Muitas tentativas de login. Tente novamente em {$seconds} segundos.",
            ]);
        }

        // Check if user exists and is active
        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, $decayMinutes * 60);
            
            Log::warning('Failed login attempt', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'user_exists' => $user ? true : false
            ]);
            
            return back()->withErrors([
                'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
            ]);
        }

        // Clear rate limiting on successful login
        RateLimiter::clear($key);
        
        // Update last login
        $user->update(['last_login_at' => now()]);
        
        Auth::login($user);
        $request->session()->regenerate();
        
        Log::info('Successful login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip()
        ]);

        return redirect()->intended('/dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {

        // Rate limiting for registration
        $key = 'register_attempts_' . $request->ip();
        $maxAttempts = 3;
        $decayMinutes = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            Log::warning('Too many registration attempts', [
                'ip' => $request->ip(),
                'email' => $request->email
            ]);
            
            return back()->withErrors([
                'email' => "Muitas tentativas de registro. Tente novamente em {$seconds} segundos.",
            ]);
        }

        try {
            $user = User::create([
                'name' => strip_tags(trim($request->name)),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
                'role' => 'user',
                'is_active' => true,
            ]);

            // Generate API token automatically
            $token = $user->generateApiToken();

            // Criar utilizador na Wuzapi com o mesmo token
            try {
                $wuzapiService = new WuzapiService($token);
                $wuzapiResult = $wuzapiService->createWuzapiUser($user->name, $token);
                
                if (!$wuzapiResult['success']) {
                    Log::warning('Falha ao criar utilizador na Wuzapi durante registro', [
                        'user' => $user->email,
                        'error' => $wuzapiResult['message']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Erro ao criar utilizador na Wuzapi', [
                    'user' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }

            Auth::login($user);
            
            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return redirect('/dashboard');
            
        } catch (\Exception $e) {
            RateLimiter::hit($key, $decayMinutes * 60);
            
            Log::error('Registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return back()->withErrors([
                'email' => 'Erro interno. Tente novamente mais tarde.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}