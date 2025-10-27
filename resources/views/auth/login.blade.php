<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - HiLeads</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md fade-in">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl shadow-lg mb-4 float-animation">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">HiLeads</h1>
            <p class="text-gray-600 text-sm">Gestão Inteligente de Leads</p>
        </div>

        <!-- Login Form Card -->
        <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Bem-vindo de volta!</h2>
                <p class="text-gray-600 text-sm">Entre com suas credenciais para continuar</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        E-mail
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" 
                           placeholder="seu@email.com">
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Senha
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="current-password" 
                           required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" 
                           placeholder="••••••••">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="remember"
                               class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Lembrar de mim</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-red-800">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Entrar
                    </span>
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Não tem uma conta?
                    <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700">
                        Criar conta gratuita
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} HiLeads. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
