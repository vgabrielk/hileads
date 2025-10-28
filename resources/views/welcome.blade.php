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
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Hero Section -->
        <section
            class="gradient-hero relative overflow-hidden min-h-screen flex items-center"
        >
            <div class="absolute inset-0 bg-black opacity-5"></div>
            
            <!-- Login/Register Links -->
            <div class="absolute top-6 right-6 z-20 flex gap-4">
                <a
                    href="{{ route('login') }}"
                    class="px-6 py-2 rounded-lg text-white font-semibold border-2 border-white/30 hover:bg-white/10 transition-colors"
                >
                    Entrar
                </a>
                <a
                    href="{{ route('register') }}"
                    class="px-6 py-2 rounded-lg bg-white text-hileads-blue font-semibold hover:bg-gray-100 transition-colors"
                >
                    Criar Conta
                </a>
            </div>

            <div
                class="container mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10"
            >
                <div class="grid lg:grid-cols-2 gap-12 items-center">
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
                        <div class="flex flex-wrap gap-6 items-center">
                            <div class="flex items-center gap-2">
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
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    ></path>
                                </svg>
                                <span class="text-sm sm:text-base font-medium"
                                    >100% Seguro</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
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
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                <span class="text-sm sm:text-base font-medium"
                                    >Em Euros (€)</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
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
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <span class="text-sm sm:text-base font-medium"
                                    >7 Dias Grátis</span
                                >
                            </div>
                        </div>

                        <!-- Spam Warning -->
                        <div
                            class="mt-6 bg-yellow-500/20 border border-yellow-500/30 rounded-lg px-4 py-3"
                        >
                            <div class="flex items-start gap-3">
                                <svg
                                    class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    ></path>
                                </svg>
                                <p
                                    class="text-sm text-yellow-100 leading-relaxed"
                                >
                                    <strong>Atenção:</strong> Enviar muitas
                                    mensagens idênticas pode ser caracterizado
                                    como spam, resultando em restrição
                                    temporária ou permanente da sua conta
                                    WhatsApp.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Hero Image -->
                    <div class="slide-in-right">
                        <div class="w-full h-96 bg-white/10 backdrop-blur-sm rounded-2xl shadow-2xl flex items-center justify-center">
                            <div class="text-center text-white p-8">
                                <img src="{{ asset('logo-horizontal.png') }}" alt="HiLeads" class="h-16 mx-auto mb-4">
                                <p class="text-lg">Dashboard Preview</p>
                            </div>
                        </div>
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
        </script>
    </body>
</html>
