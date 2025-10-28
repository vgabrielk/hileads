<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HiLeads') }} - Gestão Inteligente de Leads</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logotipo.png') }}">

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        whatsapp: {
                            green: '#25D366',
                            'green-dark': '#1da851',
                            'green-light': '#e8f5e9',
                        },
                        background: '#fafafa',
                        foreground: '#262626',
                        card: '#ffffff',
                        'card-foreground': '#262626',
                        primary: '#25D366',
                        'primary-foreground': '#ffffff',
                        secondary: '#f5f5f5',
                        'secondary-foreground': '#262626',
                        muted: '#f5f5f5',
                        'muted-foreground': '#737373',
                        accent: '#e8f5e9',
                        'accent-foreground': '#1da851',
                        destructive: '#ef4444',
                        'destructive-foreground': '#ffffff',
                        warning: '#f59e0b',
                        'warning-foreground': '#ffffff',
                        success: '#25D366',
                        'success-foreground': '#ffffff',
                        border: '#e5e5e5',
                        input: '#e5e5e5',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Button ripple effect */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }
        
        .btn-ripple::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-ripple:active::before {
            width: 300px;
            height: 300px;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f5f5f5;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #d4d4d4;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a3a3a3;
        }

        /* Custom Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-slideIn {
            animation: slideIn 0.3s ease-out;
        }

        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out;
        }

        /* Mobile Responsive Improvements */
        @media (max-width: 640px) {
            .mobile-full-width {
                width: 100% !important;
            }
            
            .mobile-stack {
                flex-direction: column !important;
            }
            
            .mobile-text-center {
                text-align: center !important;
            }
        }

        /* Smooth transitions for mobile menu */
        .mobile-menu-transition {
            transition: transform 0.3s ease-in-out;
        }

        /* Modal backdrop blur effect */
        .modal-backdrop {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Modal animations */
        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .modal-backdrop-show {
            animation: modalFadeIn 0.3s ease-out;
        }

        .modal-content-show {
            animation: modalSlideIn 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-background text-foreground">
    <div class="min-h-screen flex">
        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-card border-r border-border flex flex-col fixed h-screen z-50 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0">
            <!-- Logo -->
            <div class="p-6 border-b border-border">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('logo-horizontal.png') }}" alt="HiLeads" class="h-10">
                </div>
            </div>

            <!-- Menu Items -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    @if(!Auth::user()->isAdmin())
                        <!-- Itens para utilizadores comuns -->
                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                                </svg>
                                <span class="font-medium">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contacts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('contacts.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span class="font-medium">Contactos</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('groups.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('groups.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                                <span class="font-medium">Grupos</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mass-sendings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('mass-sendings.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <span class="font-medium">Campanhas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('whatsapp.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('whatsapp.*') && !request()->routeIs('chat.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="font-medium">WhatsApp</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('chat.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <span class="font-medium">Chat</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('plans.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('plans.index') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="font-medium">Planos</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('subscriptions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('subscriptions.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">Subscrições</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->isAdmin())
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="font-medium">Admin Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('plans.admin') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('plans.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="font-medium">Gerir Planos</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.subscriptions.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">Gerir Subscrições</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.campaigns.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.campaigns.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <span class="font-medium">Gerir Campanhas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.analytics.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="font-medium">Analytics</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="font-medium">Configurações</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.logs.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-medium">Logs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.notifications.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12 7H4.828zM4.828 17l2.586-2.586a2 2 0 012.828 0L12 17H4.828z"></path>
                                </svg>
                                <span class="font-medium">Notificações</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span class="font-medium">Utilizadores</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.wuzapi-users-page') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.wuzapi-users-page') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span class="font-medium">Wuzapi</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('profile.*') ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }} transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Info & Logout -->
            <div class="p-4 border-t border-border">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-primary">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-foreground truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-muted-foreground truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded-lg transition-colors" title="Sair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile Header -->
        <div class="lg:hidden fixed top-0 left-0 right-0 bg-card border-b border-border z-30 flex items-center justify-between p-4">
            <button id="mobile-menu-button" class="p-2 rounded-lg hover:bg-accent">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div class="flex-1"></div> <!-- Spacer for centering -->
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 lg:ml-64 overflow-auto pt-16 lg:pt-0">

            <!-- Messages/Notifications -->
            <div class="relative z-50">
                @if(session('success'))
                    <div class="m-4 animate-slideIn">
                        <div class="relative rounded-lg border border-success/20 bg-success/10 p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold text-success mb-1">Sucesso!</p>
                                <p class="text-sm text-success opacity-90">{{ session('success') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-success hover:opacity-70 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="m-4 animate-slideIn">
                        <div class="relative rounded-lg border border-destructive/20 bg-destructive/10 p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold text-destructive mb-1">Erro!</p>
                                <p class="text-sm text-destructive opacity-90">{{ session('error') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-destructive hover:opacity-70 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="m-4 animate-slideIn">
                        <div class="relative rounded-lg border border-primary/20 bg-primary/10 p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold text-primary mb-1">Informação</p>
                                <p class="text-sm text-primary opacity-90">{{ session('info') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-primary hover:opacity-70 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <!-- Mobile Menu JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            
            // Toggle mobile menu
            function toggleMobileMenu() {
                sidebar.classList.toggle('-translate-x-full');
                mobileMenuOverlay.classList.toggle('hidden');
            }
            
            // Close mobile menu
            function closeMobileMenu() {
                sidebar.classList.add('-translate-x-full');
                mobileMenuOverlay.classList.add('hidden');
            }
            
            // Event listeners
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', toggleMobileMenu);
            }
            
            if (mobileMenuOverlay) {
                mobileMenuOverlay.addEventListener('click', closeMobileMenu);
            }
            
            // Close menu when clicking on menu items (mobile)
            document.querySelectorAll('#sidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) { // lg breakpoint
                        closeMobileMenu();
                    }
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    closeMobileMenu();
                }
            });
        });
    </script>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div id="modalBackdrop" class="fixed inset-0 bg-black/50 modal-backdrop"></div>
        
        <!-- Modal Content -->
        <div id="modalContent" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-4">
            <div class="bg-card rounded-2xl shadow-2xl border border-border overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-border bg-muted/30">
                    <div class="flex items-center gap-3">
                        <div id="modalIcon" class="w-10 h-10 rounded-full flex items-center justify-center">
                            <!-- Icon will be inserted here -->
                        </div>
                        <div>
                            <h3 id="modalTitle" class="text-lg font-semibold text-foreground">Confirmar Ação</h3>
                            <p id="modalSubtitle" class="text-sm text-muted-foreground">Esta ação não pode ser desfeita</p>
                        </div>
                    </div>
                </div>
                
                <!-- Body -->
                <div class="px-6 py-4">
                    <p id="modalMessage" class="text-sm text-foreground leading-relaxed">
                        Tem certeza que deseja continuar?
                    </p>
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-muted/20 border-t border-border flex items-center justify-end gap-3">
                    <button id="modalCancel" 
                            class="px-4 py-2 text-sm font-medium text-muted-foreground bg-secondary hover:bg-secondary/80 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button id="modalConfirm" 
                            class="px-4 py-2 text-sm font-medium text-white bg-destructive hover:bg-destructive/90 rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-dismiss notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto dismiss notifications after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('[role="alert"]').forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if(alert.parentElement) alert.parentElement.remove();
                    }, 500);
                });
            }, 5000);
        });
    </script>

    <!-- Confirmation Modal JavaScript -->
    <script>
        class ConfirmationModal {
            constructor() {
                this.modal = document.getElementById('confirmationModal');
                this.backdrop = document.getElementById('modalBackdrop');
                this.content = document.getElementById('modalContent');
                this.icon = document.getElementById('modalIcon');
                this.title = document.getElementById('modalTitle');
                this.subtitle = document.getElementById('modalSubtitle');
                this.message = document.getElementById('modalMessage');
                this.cancelBtn = document.getElementById('modalCancel');
                this.confirmBtn = document.getElementById('modalConfirm');
                
                this.resolve = null;
                this.reject = null;
                
                this.init();
            }
            
            init() {
                // Event listeners
                this.cancelBtn.addEventListener('click', () => this.hide(false));
                this.confirmBtn.addEventListener('click', () => this.hide(true));
                this.backdrop.addEventListener('click', () => this.hide(false));
                
                // Close on Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !this.modal.classList.contains('hidden')) {
                        this.hide(false);
                    }
                });
            }
            
            show(options = {}) {
                return new Promise((resolve, reject) => {
                    this.resolve = resolve;
                    this.reject = reject;
                    
                    // Set content
                    this.title.textContent = options.title || 'Confirmar Ação';
                    this.subtitle.textContent = options.subtitle || 'Esta ação não pode ser desfeita';
                    this.message.textContent = options.message || 'Tem certeza que deseja continuar?';
                    this.confirmBtn.textContent = options.confirmText || 'Confirmar';
                    this.cancelBtn.textContent = options.cancelText || 'Cancelar';
                    
                    // Set icon and colors based on type
                    this.setIconAndColors(options.type || 'warning');
                    
                    // Show modal
                    this.modal.classList.remove('hidden');
                    this.backdrop.classList.add('modal-backdrop-show');
                    this.content.classList.add('modal-content-show');
                    
                    // Focus on confirm button
                    setTimeout(() => this.confirmBtn.focus(), 100);
                });
            }
            
            hide(confirmed) {
                this.modal.classList.add('hidden');
                this.backdrop.classList.remove('modal-backdrop-show');
                this.content.classList.remove('modal-content-show');
                
                if (this.resolve) {
                    this.resolve(confirmed);
                    this.resolve = null;
                    this.reject = null;
                }
            }
            
            setIconAndColors(type) {
                const configs = {
                    warning: {
                        icon: `<svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>`,
                        iconBg: 'bg-warning/10',
                        confirmBg: 'bg-warning hover:bg-warning/90'
                    },
                    danger: {
                        icon: `<svg class="w-5 h-5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>`,
                        iconBg: 'bg-destructive/10',
                        confirmBg: 'bg-destructive hover:bg-destructive/90'
                    },
                    info: {
                        icon: `<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>`,
                        iconBg: 'bg-primary/10',
                        confirmBg: 'bg-primary hover:bg-primary/90'
                    },
                    success: {
                        icon: `<svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>`,
                        iconBg: 'bg-success/10',
                        confirmBg: 'bg-success hover:bg-success/90'
                    }
                };
                
                const config = configs[type] || configs.warning;
                this.icon.innerHTML = config.icon;
                this.icon.className = `w-10 h-10 rounded-full flex items-center justify-center ${config.iconBg}`;
                this.confirmBtn.className = `px-4 py-2 text-sm font-medium text-white ${config.confirmBg} rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5`;
            }
        }
        
        // Global modal instance
        window.confirmationModal = new ConfirmationModal();
        
        // Global function for easy use
        window.confirmAction = function(options) {
            return window.confirmationModal.show(options);
        };
    </script>
</body>
</html>
