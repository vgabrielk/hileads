<!doctype html>
<html lang="pt" class="scroll-smooth">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>HiLeads - Automatize o seu marketing no WhatsApp</title>
        <meta
            name="description"
            content="Automatize campanhas de marketing no WhatsApp com o HiLeads. Envie mensagens em massa, extraia leads e gere conversões de forma inteligente. Experimente grátis por 7 dias."
        />

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logotipo.png') }}">

        <!-- Open Graph -->
        <meta
            property="og:title"
            content="HiLeads - Automatize o seu marketing no WhatsApp"
        />
        <meta
            property="og:description"
            content="Plataforma de automação de marketing no WhatsApp. Campanhas em massa, gestão de leads e automação inteligente."
        />
        <meta property="og:type" content="website" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet"
        />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
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
                    },
                },
            };
        </script>

        <style>
            * {
                scroll-behavior: smooth;
            }

            .gradient-hero {
                background: linear-gradient(135deg, #0ea5e9 0%, #25d366 100%);
            }

            .gradient-cta {
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            }

            .fade-in {
                opacity: 0;
                transform: translateY(30px);
                transition:
                    opacity 0.8s ease-out,
                    transform 0.8s ease-out;
            }

            .fade-in.visible {
                opacity: 1;
                transform: translateY(0);
            }

            .slide-in-left {
                opacity: 0;
                transform: translateX(-50px);
                transition:
                    opacity 0.8s ease-out,
                    transform 0.8s ease-out;
            }

            .slide-in-left.visible {
                opacity: 1;
                transform: translateX(0);
            }

            .slide-in-right {
                opacity: 0;
                transform: translateX(50px);
                transition:
                    opacity 0.8s ease-out,
                    transform 0.8s ease-out;
            }

            .slide-in-right.visible {
                opacity: 1;
                transform: translateX(0);
            }

            .pricing-card {
                transition:
                    transform 0.3s ease,
                    box-shadow 0.3s ease;
            }

            .pricing-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }

            .feature-card {
                transition:
                    transform 0.3s ease,
                    box-shadow 0.3s ease;
            }

            .feature-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .btn-primary {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
            }

            .btn-secondary {
                transition: all 0.3s ease;
            }

            .btn-secondary:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }

            /* Header styles */
            .header-blur {
                background: transparent;
                transition: all 0.3s ease;
            }

            .header-scrolled {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            .header-scrolled .text-gray-700 {
                color: #374151;
            }

            .header-transparent .text-gray-700 {
                color: white;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Header -->
        <header class="header-blur header-transparent fixed top-0 left-0 right-0 z-50 border-b border-white/10">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="/" class="flex items-center">
                            <img src="{{ asset('logo-horizontal.png') }}" alt="HiLeads" class="h-8 brightness-0 invert">
                        </a>
                    </div>

                    <!-- Navigation Links (Desktop) -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#benefits" class="nav-link text-white hover:text-white/80 font-medium transition-colors">
                            Funcionalidades
                        </a>
                        <a href="#pricing" class="nav-link text-white hover:text-white/80 font-medium transition-colors">
                            Preços
                        </a>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="flex items-center gap-3">
                        <a
                            href="{{ route('login') }}"
                            class="auth-login px-6 py-2.5 rounded-lg text-white font-semibold hover:bg-white/10 transition-colors"
                        >
                            Entrar
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="px-6 py-2.5 rounded-lg bg-white text-hileads-blue font-semibold hover:shadow-lg transition-all"
                        >
                            Criar Conta
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section
            class="gradient-hero relative overflow-hidden min-h-screen flex items-center pt-20"
        >

            <div
                class="container mx-auto px-4 sm:px-6 lg:px-8  relative z-10"
            >
                <div class="grid lg:grid-cols-2 gap-12 items-start">
                    <!-- Left Column: Content -->
                    <div class="text-white slide-in-left">
                        <h1
                            class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6"
                        >
                            Automatize o seu marketing no WhatsApp com o HiLeads
                        </h1>
                        <p
                            class="text-xl sm:text-2xl text-white/90 mb-8 leading-relaxed"
                        >
                            Envie campanhas em massa, extraia leads e gere
                            conversões de forma inteligente.
                        </p>

                        <!-- CTA Button -->
                        <div class="mb-10">
                            <a
                                href="{{ route('register') }}"
                                class="btn-primary inline-block px-8 py-4 rounded-lg text-white font-semibold text-lg shadow-xl"
                            >
                                Experimentar Gratuitamente
                            </a>
                        </div>

                        <!-- Trust Badges -->
                       
                    </div>

                    <!-- Right Column: Hero Image -->
                    <div class="slide-in-right">
                        <img
                            src="{{ asset('dashboard.png') }}"
                            alt="Dashboard HiLeads mostrando estatísticas de campanhas WhatsApp"
                            class="w-full rounded-2xl shadow-2xl"
                        />
                    </div>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div
                class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce"
            >
                <svg
                    class="w-6 h-6 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3"
                    ></path>
                </svg>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-20 lg:py-32 bg-white" id="benefits">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in">
                    <h2
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4"
                    >
                        Por que escolher o HiLeads?
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Tudo o que você precisa para automatizar seu marketing no WhatsApp
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Benefit 1 -->
                    <div
                        class="feature-card bg-white p-8 rounded-xl border border-gray-200 fade-in"
                        data-testid="card-benefit-campaigns"
                    >
                        <div
                            class="w-16 h-16 bg-hileads-blue/10 rounded-lg flex items-center justify-center mb-6"
                        >
                            <svg
                                class="w-8 h-8 text-hileads-blue"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                ></path>
                            </svg>
                        </div>
                        <h3
                            class="font-display text-xl font-semibold text-gray-900 mb-3"
                        >
                            Campanhas em Massa
                        </h3>
                        <p class="text-gray-600">
                            Envie mensagens personalizadas em larga escala de
                            forma eficiente e profissional.
                        </p>
                    </div>

                    <!-- Benefit 2 -->
                    <div
                        class="feature-card bg-white p-8 rounded-xl border border-gray-200 fade-in"
                        style="transition-delay: 0.1s"
                        data-testid="card-benefit-leads"
                    >
                        <div
                            class="w-16 h-16 bg-whatsapp-green/10 rounded-lg flex items-center justify-center mb-6"
                        >
                            <svg
                                class="w-8 h-8 text-whatsapp-green"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                ></path>
                            </svg>
                        </div>
                        <h3
                            class="font-display text-xl font-semibold text-gray-900 mb-3"
                        >
                            Gestão de Leads
                        </h3>
                        <p class="text-gray-600">
                            Capture e segmente contactos automaticamente para
                            campanhas mais eficazes.
                        </p>
                    </div>

                    <!-- Benefit 3 -->
                    <div
                        class="feature-card bg-white p-8 rounded-xl border border-gray-200 fade-in"
                        style="transition-delay: 0.2s"
                        data-testid="card-benefit-analytics"
                    >
                        <div
                            class="w-16 h-16 bg-hileads-blue/10 rounded-lg flex items-center justify-center mb-6"
                        >
                            <svg
                                class="w-8 h-8 text-hileads-blue"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                ></path>
                            </svg>
                        </div>
                        <h3
                            class="font-display text-xl font-semibold text-gray-900 mb-3"
                        >
                            Relatórios e Métricas
                        </h3>
                        <p class="text-gray-600">
                            Acompanhe o desempenho das suas campanhas com
                            análises detalhadas em tempo real.
                        </p>
                    </div>

                    <!-- Benefit 4 -->
                    <div
                        class="feature-card bg-white p-8 rounded-xl border border-gray-200 fade-in"
                        style="transition-delay: 0.3s"
                        data-testid="card-benefit-automation"
                    >
                        <div
                            class="w-16 h-16 bg-whatsapp-green/10 rounded-lg flex items-center justify-center mb-6"
                        >
                            <svg
                                class="w-8 h-8 text-whatsapp-green"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"
                                ></path>
                            </svg>
                        </div>
                        <h3
                            class="font-display text-xl font-semibold text-gray-900 mb-3"
                        >
                            Automação Inteligente
                        </h3>
                        <p class="text-gray-600">
                            Respostas automáticas e fluxos de mensagem que
                            poupam tempo e aumentam conversões.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-20 lg:py-32 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in">
                    <h2
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4"
                    >
                        Como funciona?
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Comece a automatizar em apenas 3 passos simples
                    </p>
                </div>

                <div class="max-w-5xl mx-auto">
                    <div class="grid md:grid-cols-3 gap-8">
                        <!-- Step 1 -->
                        <div class="text-center fade-in">
                            <div class="relative mb-8">
                                <div class="w-20 h-20 bg-gradient-to-br from-hileads-blue to-blue-600 rounded-full flex items-center justify-center mx-auto shadow-xl">
                                    <span class="text-3xl font-bold text-white">1</span>
                                </div>
                                <div class="hidden md:block absolute top-10 left-1/2 w-full h-0.5 bg-gradient-to-r from-hileads-blue to-transparent"></div>
                            </div>
                            <h3 class="font-display text-xl font-bold text-gray-900 mb-3">
                                Conecte seu WhatsApp
                            </h3>
                            <p class="text-gray-600">
                                Escaneie o QR code e conecte sua conta WhatsApp em segundos. Rápido e seguro.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="text-center fade-in" style="transition-delay: 0.1s">
                            <div class="relative mb-8">
                                <div class="w-20 h-20 bg-gradient-to-br from-hileads-blue to-blue-600 rounded-full flex items-center justify-center mx-auto shadow-xl">
                                    <span class="text-3xl font-bold text-white">2</span>
                                </div>
                                <div class="hidden md:block absolute top-10 left-1/2 w-full h-0.5 bg-gradient-to-r from-hileads-blue to-transparent"></div>
                            </div>
                            <h3 class="font-display text-xl font-bold text-gray-900 mb-3">
                                Importe seus contactos
                            </h3>
                            <p class="text-gray-600">
                                Carregue sua lista de contactos via CSV ou extraia diretamente de grupos WhatsApp.
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="text-center fade-in" style="transition-delay: 0.2s">
                            <div class="relative mb-8">
                                <div class="w-20 h-20 bg-gradient-to-br from-whatsapp-green to-green-600 rounded-full flex items-center justify-center mx-auto shadow-xl">
                                    <span class="text-3xl font-bold text-white">3</span>
                                </div>
                            </div>
                            <h3 class="font-display text-xl font-bold text-gray-900 mb-3">
                                Lance sua campanha
                            </h3>
                            <p class="text-gray-600">
                                Crie mensagens personalizadas e comece a enviar. Acompanhe resultados em tempo real.
                            </p>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="text-center mt-16 fade-in">
                        <a
                            href="{{ route('register') }}"
                            class="btn-primary inline-block px-10 py-4 rounded-lg text-white font-semibold text-lg shadow-xl"
                        >
                            Começar Agora - É Grátis
                        </a>
                        <p class="text-sm text-gray-500 mt-4">
                            Sem cartão de crédito necessário
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid Section -->
        <section class="py-20 lg:py-32 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in">
                    <h2
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4"
                    >
                        Recursos Completos
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Todas as ferramentas que você precisa para dominar o WhatsApp marketing
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                    <!-- Feature 1 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in">
                        <div class="flex-shrink-0 w-12 h-12 bg-hileads-blue/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-hileads-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Envios Programados</h4>
                            <p class="text-sm text-gray-600">Agende campanhas para horários específicos</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in" style="transition-delay: 0.05s">
                        <div class="flex-shrink-0 w-12 h-12 bg-whatsapp-green/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-whatsapp-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Mensagens Personalizadas</h4>
                            <p class="text-sm text-gray-600">Use variáveis para personalizar cada mensagem</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in" style="transition-delay: 0.1s">
                        <div class="flex-shrink-0 w-12 h-12 bg-hileads-blue/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-hileads-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Envio de Mídias</h4>
                            <p class="text-sm text-gray-600">Imagens, vídeos, áudios e documentos</p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in" style="transition-delay: 0.15s">
                        <div class="flex-shrink-0 w-12 h-12 bg-whatsapp-green/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-whatsapp-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Gestão de Grupos</h4>
                            <p class="text-sm text-gray-600">Organize contactos em grupos personalizados</p>
                        </div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in" style="transition-delay: 0.2s">
                        <div class="flex-shrink-0 w-12 h-12 bg-hileads-blue/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-hileads-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Status de Entrega</h4>
                            <p class="text-sm text-gray-600">Acompanhe entregas, leituras e erros</p>
                        </div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in" style="transition-delay: 0.25s">
                        <div class="flex-shrink-0 w-12 h-12 bg-whatsapp-green/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-whatsapp-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Analytics Detalhados</h4>
                            <p class="text-sm text-gray-600">Métricas e relatórios em tempo real</p>
                        </div>
                    </div>

                    <!-- Feature 7 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in" style="transition-delay: 0.3s">
                        <div class="flex-shrink-0 w-12 h-12 bg-hileads-blue/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-hileads-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Intervalos Inteligentes</h4>
                            <p class="text-sm text-gray-600">Evite bloqueios com envios espaçados</p>
                        </div>
                    </div>

                    <!-- Feature 8 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in" style="transition-delay: 0.35s">
                        <div class="flex-shrink-0 w-12 h-12 bg-whatsapp-green/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-whatsapp-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Histórico Completo</h4>
                            <p class="text-sm text-gray-600">Acesse todas as suas campanhas anteriores</p>
                        </div>
                    </div>

                    <!-- Feature 9 -->
                    <div class="flex items-start gap-4 p-6 bg-gray-50 rounded-xl fade-in" style="transition-delay: 0.4s">
                        <div class="flex-shrink-0 w-12 h-12 bg-hileads-blue/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-hileads-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">100% Seguro</h4>
                            <p class="text-sm text-gray-600">Seus dados protegidos com criptografia</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-20 lg:py-32 bg-gray-50" id="pricing">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in">
                    <h2
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4"
                    >
                        Escolha o plano ideal para o seu negócio
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Experimente qualquer plano gratuitamente durante 7 dias
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <!-- Starter Plan -->
                    <div
                        class="pricing-card bg-white border border-gray-200 rounded-2xl p-8 fade-in"
                        data-testid="card-plan-starter"
                    >
                        <div class="mb-6">
                            <h3
                                class="font-display text-2xl font-bold text-gray-900 mb-2"
                            >
                                Starter
                            </h3>
                            <div class="flex items-baseline gap-2">
                                <span class="text-5xl font-bold text-gray-900"
                                    >€19</span
                                >
                                <span class="text-gray-600">/mês</span>
                            </div>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >1.000 mensagens/mês</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >1 conta WhatsApp</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >Suporte básico</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >Relatórios básicos</span
                                >
                            </li>
                        </ul>

                        <a
                            href="{{ route('register') }}"
                            class="block w-full text-center px-6 py-3 bg-gray-100 text-gray-900 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                            data-testid="button-try-starter"
                        >
                            Experimentar 7 dias grátis
                        </a>
                    </div>

                    <!-- Pro Plan (Featured) -->
                    <div
                        class="pricing-card bg-gradient-to-br from-hileads-blue to-blue-600 border-4 border-hileads-blue rounded-2xl p-8 relative transform md:scale-105 fade-in"
                        style="transition-delay: 0.1s"
                        data-testid="card-plan-pro"
                    >
                        <div
                            class="absolute -top-4 left-1/2 transform -translate-x-1/2"
                        >
                            <span
                                class="bg-whatsapp-green text-white px-4 py-1 rounded-full text-sm font-semibold"
                            >
                                MAIS POPULAR
                            </span>
                        </div>

                        <div class="mb-6">
                            <h3
                                class="font-display text-2xl font-bold text-white mb-2"
                            >
                                Pro
                            </h3>
                            <div class="flex items-baseline gap-2">
                                <span class="text-5xl font-bold text-white"
                                    >€49</span
                                >
                                <span class="text-white/80">/mês</span>
                            </div>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-white mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-white"
                                    >10.000 mensagens/mês</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-white mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-white"
                                    >3 contas WhatsApp</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-white mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-white"
                                    >Automações avançadas</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-white mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-white"
                                    >Relatórios completos</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-white mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-white"
                                    >Suporte prioritário</span
                                >
                            </li>
                        </ul>

                        <a
                            href="{{ route('register') }}"
                            class="block w-full text-center px-6 py-3 bg-white text-hileads-blue rounded-lg font-semibold hover:bg-gray-100 transition-colors"
                            data-testid="button-try-pro"
                        >
                            Experimentar 7 dias grátis
                        </a>
                    </div>

                    <!-- Business Plan -->
                    <div
                        class="pricing-card bg-white border border-gray-200 rounded-2xl p-8 fade-in"
                        style="transition-delay: 0.2s"
                        data-testid="card-plan-business"
                    >
                        <div class="mb-6">
                            <h3
                                class="font-display text-2xl font-bold text-gray-900 mb-2"
                            >
                                Business
                            </h3>
                            <div class="flex items-baseline gap-2">
                                <span class="text-5xl font-bold text-gray-900"
                                    >€99</span
                                >
                                <span class="text-gray-600">/mês</span>
                            </div>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >Mensagens ilimitadas</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >10 contas WhatsApp</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >Integrações completas</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >Suporte dedicado 24/7</span
                                >
                            </li>
                            <li class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-whatsapp-green mt-0.5 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-gray-700"
                                    >Gestor de conta dedicado</span
                                >
                            </li>
                        </ul>

                        <a
                            href="{{ route('register') }}"
                            class="block w-full text-center px-6 py-3 bg-gray-100 text-gray-900 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                            data-testid="button-try-business"
                        >
                            Experimentar 7 dias grátis
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-20 lg:py-32 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in">
                    <h2
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4"
                    >
                        O que dizem nossos clientes
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Empresas reais com resultados reais usando o HiLeads
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <!-- Testimonial 1 -->
                    <div class="bg-gray-50 p-8 rounded-2xl fade-in">
                        <div class="flex items-center gap-1 mb-4">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            "O HiLeads transformou completamente a nossa estratégia de vendas. Aumentámos as conversões em 250% nos primeiros 3 meses!"
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-hileads-blue to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                AS
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Ana Silva</h4>
                                <p class="text-sm text-gray-600">CEO, TechVendas Lda</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-gray-50 p-8 rounded-2xl fade-in" style="transition-delay: 0.1s">
                        <div class="flex items-center gap-1 mb-4">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            "Incrível! A automação poupa-nos horas todos os dias. Interface intuitiva e suporte sempre disponível."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-whatsapp-green to-green-600 rounded-full flex items-center justify-center text-white font-bold">
                                PM
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Pedro Martins</h4>
                                <p class="text-sm text-gray-600">Fundador, Digital Growth</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-gray-50 p-8 rounded-2xl fade-in" style="transition-delay: 0.2s">
                        <div class="flex items-center gap-1 mb-4">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            "Conseguimos gerir milhares de leads sem esforço. Os relatórios são excelentes e ajudam muito na tomada de decisões."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-hileads-blue to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                CR
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Carla Rodrigues</h4>
                                <p class="text-sm text-gray-600">Dir. Marketing, EcomPro</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-20 lg:py-32 bg-gray-50" id="faq">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
                <div class="text-center mb-16 fade-in">
                    <h2
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4"
                    >
                        Perguntas Frequentes
                    </h2>
                    <p class="text-xl text-gray-600">
                        Respostas às dúvidas mais comuns sobre o HiLeads
                    </p>
                </div>

                <div class="space-y-4 fade-in">
                    <!-- FAQ 1 -->
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <button
                            class="faq-question w-full px-6 py-5 text-left hover:bg-gray-50 transition-colors flex justify-between items-center"
                            onclick="toggleFAQ(this)"
                        >
                            <span class="font-semibold text-gray-900 pr-8">Como funciona o período de teste gratuito?</span>
                            <svg
                                class="w-5 h-5 text-gray-500 transform transition-transform flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                ></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 pb-5">
                            <p class="text-gray-700">
                                Oferecemos 7 dias de teste completamente gratuito em qualquer plano. Você tem acesso total a todos os recursos sem precisar inserir cartão de crédito. Após o período de teste, você decide se quer continuar.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <button
                            class="faq-question w-full px-6 py-5 text-left hover:bg-gray-50 transition-colors flex justify-between items-center"
                            onclick="toggleFAQ(this)"
                        >
                            <span class="font-semibold text-gray-900 pr-8">O WhatsApp pode bloquear minha conta?</span>
                            <svg
                                class="w-5 h-5 text-gray-500 transform transition-transform flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                ></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 pb-5">
                            <p class="text-gray-700">
                                Existe esse risco se você enviar mensagens de forma inadequada. O HiLeads oferece recursos de personalização, intervalos inteligentes e gestão de limites para minimizar riscos. É fundamental seguir as boas práticas de marketing e não enviar spam.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <button
                            class="faq-question w-full px-6 py-5 text-left hover:bg-gray-50 transition-colors flex justify-between items-center"
                            onclick="toggleFAQ(this)"
                        >
                            <span class="font-semibold text-gray-900 pr-8">Posso usar com vários números de WhatsApp?</span>
                            <svg
                                class="w-5 h-5 text-gray-500 transform transition-transform flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                ></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 pb-5">
                            <p class="text-gray-700">
                                Sim! Dependendo do plano escolhido, você pode gerenciar 1, 3 ou até 10 contas WhatsApp simultaneamente. Isso permite segmentar campanhas por marca, departamento ou região.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <button
                            class="faq-question w-full px-6 py-5 text-left hover:bg-gray-50 transition-colors flex justify-between items-center"
                            onclick="toggleFAQ(this)"
                        >
                            <span class="font-semibold text-gray-900 pr-8">Posso cancelar a qualquer momento?</span>
                            <svg
                                class="w-5 h-5 text-gray-500 transform transition-transform flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                ></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 pb-5">
                            <p class="text-gray-700">
                                Absolutamente. Você pode cancelar sua assinatura a qualquer momento sem penalizações ou custos adicionais. O acesso continuará até o final do período pago.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <button
                            class="faq-question w-full px-6 py-5 text-left hover:bg-gray-50 transition-colors flex justify-between items-center"
                            onclick="toggleFAQ(this)"
                        >
                            <span class="font-semibold text-gray-900 pr-8">Que tipo de suporte está disponível?</span>
                            <svg
                                class="w-5 h-5 text-gray-500 transform transition-transform flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                ></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 pb-5">
                            <p class="text-gray-700">
                                Oferecemos suporte por email e chat em todos os planos. Os planos Pro e Business incluem suporte prioritário, e o plano Business conta ainda com um gestor de conta dedicado disponível 24/7.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="gradient-cta py-20 lg:py-32 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-hileads-blue/20 to-whatsapp-green/20"
                ></div>
            </div>
            <div
                class="container mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10"
            >
                <div class="max-w-4xl mx-auto fade-in">
                    <h2
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6"
                    >
                        Pronto para aumentar as suas vendas pelo WhatsApp?
                    </h2>
                    <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto">
                        Junte-se a centenas de empresas que já transformaram o
                        seu marketing com o HiLeads
                    </p>
                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-center items-center"
                    >
                        <a
                            href="{{ route('register') }}"
                            class="btn-primary inline-block px-10 py-4 rounded-lg text-white font-semibold text-lg shadow-xl"
                            data-testid="button-cta-final"
                        >
                            Comece Gratuitamente Agora
                        </a>
                        <a
                            href="{{ route('login') }}"
                            class="btn-secondary inline-block px-10 py-4 rounded-lg text-white font-semibold text-lg border-2 border-white/30"
                            data-testid="button-learn-more"
                        >
                            Já tenho uma conta
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <img src="{{ asset('logo-horizontal.png') }}" alt="HiLeads" class="h-12 mx-auto mb-6 brightness-0 invert">
                    <p class="text-gray-400 text-sm mb-4">
                        Automatização de marketing profissional para WhatsApp
                    </p>
                    <p class="text-gray-400 text-sm">
                        © 2025 HiLeads. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </footer>

        <!-- JavaScript for Animations & Interactions -->
        <script>
            // Scroll Animation Observer
            const observerOptions = {
                threshold: 0.1,
                rootMargin: "0px 0px -50px 0px",
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                    }
                });
            }, observerOptions);

            // Observe all elements with animation classes
            document
                .querySelectorAll(".fade-in, .slide-in-left, .slide-in-right")
                .forEach((el) => {
                    observer.observe(el);
                });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
                anchor.addEventListener("click", function (e) {
                    const href = this.getAttribute("href");
                    if (href !== "#" && href !== "") {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({
                                behavior: "smooth",
                                block: "start",
                            });
                        }
                    }
                });
            });

            // FAQ Toggle Function
            function toggleFAQ(button) {
                const answer = button.nextElementSibling;
                const icon = button.querySelector('svg');
                const isOpen = !answer.classList.contains('hidden');

                // Close all other FAQs
                document.querySelectorAll('.faq-answer').forEach((a) => {
                    if (a !== answer) {
                        a.classList.add('hidden');
                    }
                });
                document.querySelectorAll('.faq-question svg').forEach((i) => {
                    if (i !== icon) {
                        i.classList.remove('rotate-180');
                    }
                });

                // Toggle current FAQ
                if (isOpen) {
                    answer.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                } else {
                    answer.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                }
            }

            // Header scroll effect
            const header = document.querySelector('header');
            const logo = header.querySelector('img');
            const navLinks = header.querySelectorAll('.nav-link');
            const authLogin = header.querySelector('.auth-login');
            let lastScroll = 0;

            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;
                
                if (currentScroll > 50) {
                    // Scrolled state
                    header.classList.add('header-scrolled');
                    header.classList.remove('header-transparent');
                    header.classList.remove('border-white/10');
                    header.classList.add('border-gray-100');
                    
                    // Update logo
                    logo.classList.remove('brightness-0', 'invert');
                    
                    // Update nav links
                    navLinks.forEach(link => {
                        link.classList.remove('text-white', 'hover:text-white/80');
                        link.classList.add('text-gray-700', 'hover:text-hileads-blue');
                    });
                    
                    // Update auth button
                    authLogin.classList.remove('text-white', 'hover:bg-white/10');
                    authLogin.classList.add('text-gray-700', 'hover:bg-gray-100');
                } else {
                    // Transparent state
                    header.classList.remove('header-scrolled');
                    header.classList.add('header-transparent');
                    header.classList.add('border-white/10');
                    header.classList.remove('border-gray-100');
                    
                    // Update logo
                    logo.classList.add('brightness-0', 'invert');
                    
                    // Update nav links
                    navLinks.forEach(link => {
                        link.classList.add('text-white', 'hover:text-white/80');
                        link.classList.remove('text-gray-700', 'hover:text-hileads-blue');
                    });
                    
                    // Update auth button
                    authLogin.classList.add('text-white', 'hover:bg-white/10');
                    authLogin.classList.remove('text-gray-700', 'hover:bg-gray-100');
                }
                
                lastScroll = currentScroll;
            });
        </script>
    </body>
</html>
