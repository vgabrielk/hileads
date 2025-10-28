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
            50% { transform: translateY(-20px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
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
<body class="min-h-screen overflow-hidden">
    <div class="flex min-h-screen">
        <!-- Left Side - Gradient with Content -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10 flex flex-col justify-center px-12 py-12 text-white">
                <!-- Logo -->
                <div class="mb-12">
                    <a href="/">
                        <img src="{{ asset('logo-horizontal.png') }}" alt="HiLeads" class="h-10 brightness-0 invert">
                    </a>
                </div>

                <!-- Main Content -->
                <div class="max-w-md">
                    <h1 class="font-display text-4xl font-bold mb-6">
                        Bem-vindo de volta ao HiLeads
                    </h1>
                    <p class="text-xl text-white/90 mb-12 leading-relaxed">
                        Continue gerenciando seus leads e automatizando suas campanhas de WhatsApp.
                    </p>

                    <!-- Features -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-white/90">Campanhas em massa</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-white/90">Gestão inteligente de leads</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-white/90">Relatórios em tempo real</span>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Preview -->
                <div class="mt-12 float-animation">
                    <img src="{{ asset('dashboard.png') }}" alt="Dashboard" class="rounded-xl shadow-2xl border border-white/20">
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <a href="/">
                        <img src="{{ asset('logo-horizontal.png') }}" alt="HiLeads" class="h-10 mx-auto">
                    </a>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <div class="mb-8">
                        <h2 class="text-2xl font-display font-bold text-gray-900 mb-2">Entrar na sua conta</h2>
                        <p class="text-gray-600 text-sm">Continue de onde parou</p>
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-hileads-blue focus:border-hileads-blue transition-all" 
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-hileads-blue focus:border-hileads-blue transition-all" 
                                placeholder="••••••••"
                            >
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center cursor-pointer group">
                                <input 
                                    type="checkbox" 
                                    name="remember"
                                    class="rounded border-gray-300 text-hileads-blue focus:ring-hileads-blue"
                                >
                                <span class="ml-2 text-gray-600 group-hover:text-gray-900">
                                    Lembrar-me
                                </span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="font-medium text-hileads-blue hover:text-blue-700">
                                    Esqueceu a senha?
                                </a>
                            @endif
                        </div>

                        <!-- Errors -->
                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                                <div class="text-sm text-red-800">
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Submit -->
                        <button 
                            type="submit" 
                            class="w-full py-3.5 bg-gradient-to-r from-hileads-blue to-blue-600 hover:from-blue-600 hover:to-hileads-blue text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                        >
                            Entrar
                        </button>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-6 text-center text-sm">
                        <span class="text-gray-600">Não tem uma conta?</span>
                        <a href="{{ route('register') }}" class="font-semibold text-hileads-blue hover:text-blue-700 ml-1">
                            Criar conta grátis
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500">
                        © {{ date('Y') }} HiLeads. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
