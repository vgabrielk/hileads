<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - HiLeads</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logotipo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #25d366 100%);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "hileads-blue": "#0EA5E9",
                        "whatsapp-green": "#25D366",
                    },
                    fontFamily: {
                        sans: ["Inter", "system-ui", "sans-serif"],
                        display: ["Poppins", "system-ui", "sans-serif"],
                    },
                }
            }
        }
    </script>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8 fade-in-up">
            <a href="/" class="inline-block float-animation">
                <img src="{{ asset('logo-horizontal.png') }}" alt="HiLeads" class="h-12 brightness-0 invert mx-auto">
            </a>
            <p class="text-white/90 text-sm mt-3">Gestão Inteligente de Leads</p>
        </div>

        <!-- Login Card -->
        <div class="glass-effect rounded-3xl shadow-2xl p-8 border border-white/20 fade-in-up" style="animation-delay: 0.2s">
            <div class="mb-8">
                <h2 class="text-3xl font-display font-bold text-gray-900 mb-2">Bem-vindo de volta!</h2>
                <p class="text-gray-600">Entre para continuar gerenciando seus leads</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        E-mail
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3.5 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-hileads-blue focus:border-hileads-blue transition-all" 
                        placeholder="seu@email.com"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Senha
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="current-password" 
                        required 
                        class="w-full px-4 py-3.5 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-hileads-blue focus:border-hileads-blue transition-all" 
                        placeholder="••••••••"
                    >
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer group">
                        <input 
                            type="checkbox" 
                            name="remember"
                            class="rounded border-gray-300 text-hileads-blue shadow-sm focus:border-hileads-blue focus:ring focus:ring-hileads-blue/20"
                        >
                        <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">
                            Lembrar-me
                        </span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-hileads-blue hover:text-blue-700 transition-colors">
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>

                <!-- Errors -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-red-800">
                                @foreach ($errors->all() as $error)
                                    <p class="mb-1">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Submit -->
                <button 
                    type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-hileads-blue to-blue-600 hover:from-blue-600 hover:to-hileads-blue text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                >
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Entrar
                    </span>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">ou</span>
                </div>
            </div>

            <!-- Register -->
            <div class="text-center">
                <p class="text-gray-600">
                    Não tem uma conta?
                    <a href="{{ route('register') }}" class="font-semibold text-hileads-blue hover:text-blue-700 transition-colors">
                        Criar conta grátis
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center fade-in-up" style="animation-delay: 0.4s">
            <p class="text-sm text-white/70">
                © {{ date('Y') }} HiLeads. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
