<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Criar Conta - HiLeads</title>

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
<body class="min-h-screen overflow-y-auto lg:overflow-hidden">
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
                        Comece gratuitamente hoje
                    </h1>
                    <p class="text-xl text-white/90 mb-8 leading-relaxed">
                        Automatize suas campanhas de WhatsApp e gere mais conversões em minutos.
                    </p>

                    <!-- Benefits -->
                    

                    <!-- Testimonial -->
                  
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-8 bg-gray-50">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-6">
                    <a href="/">
                        <img src="{{ asset('logo-horizontal.png') }}" alt="HiLeads" class="h-10 mx-auto">
                    </a>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-display font-bold text-gray-900 mb-1">Criar sua conta</h2>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Nome Completo
                            </label>
                            <input 
                                id="name" 
                                name="name" 
                                type="text" 
                                autocomplete="name" 
                                required 
                                value="{{ old('name') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-hileads-blue focus:border-hileads-blue transition-all" 
                                placeholder="Seu nome completo"
                            >
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                E-mail
                            </label>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                required 
                                value="{{ old('email') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-hileads-blue focus:border-hileads-blue transition-all" 
                                placeholder="seu@email.com"
                            >
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Senha
                            </label>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                autocomplete="new-password" 
                                required 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-hileads-blue focus:border-hileads-blue transition-all" 
                                placeholder="Mínimo 8 caracteres"
                            >
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Confirmar Senha
                            </label>
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                autocomplete="new-password" 
                                required 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-hileads-blue focus:border-hileads-blue transition-all" 
                                placeholder="Confirme sua senha"
                            >
                        </div>

                        <!-- Errors -->
                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                                <div class="text-sm text-red-800">
                                    @foreach ($errors->all() as $error)
                                        <p class="text-xs">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Terms -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5 mt-0.5">
                                <input 
                                    id="terms" 
                                    name="terms" 
                                    type="checkbox" 
                                    required
                                    class="rounded border-gray-300 text-hileads-blue focus:ring-hileads-blue"
                                >
                            </div>
                            <label for="terms" class="ml-2 text-xs text-gray-600 leading-relaxed">
                                Concordo com os 
                                <a href="#" class="text-hileads-blue hover:text-blue-700 font-medium">Termos</a> 
                                e 
                                <a href="#" class="text-hileads-blue hover:text-blue-700 font-medium">Privacidade</a>
                            </label>
                        </div>

                        <!-- Submit -->
                        <button 
                            type="submit" 
                            class="w-full py-3 bg-gradient-to-r from-whatsapp-green to-green-600 hover:from-green-600 hover:to-whatsapp-green text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                        >
                            Criar Conta Grátis
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-6 text-center text-sm">
                        <span class="text-gray-600">Já tem uma conta?</span>
                        <a href="{{ route('login') }}" class="font-semibold text-hileads-blue hover:text-blue-700 ml-1">
                            Fazer login
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        © {{ date('Y') }} HiLeads. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
