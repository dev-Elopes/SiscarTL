<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F9FAFB]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AutoCare') }} - Gerenciamento Veicular</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full font-['Inter',sans-serif] text-[#1F2937] antialiased bg-[#F9FAFB] selection:bg-[#E11D48] selection:text-white pb-12">

        <!-- Top SaaS Navigation Bar -->
        <header class="bg-white border-b border-[#E5E7EB] sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    
                    <!-- Left: Brand Logo & Navigation Links -->
                    <div class="flex items-center gap-4 lg:gap-6">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#E11D48]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l3-3m-3 3a2 2 0 11-2.828-2.828A2 2 0 0112 14z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a9 9 0 10-14.856 0" />
                                </svg>
                            </div>
                            <span class="font-bold text-base text-gray-900 tracking-tight">
                                Auto<span class="text-[#E11D48]">Care</span>
                            </span>
                        </a>

                        <!-- Desktop Navigation Links -->
                        <nav class="hidden lg:flex items-center gap-1">
                            <a href="{{ route('dashboard') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request()->routeIs('dashboard') ? 'bg-rose-50 text-[#E11D48]' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                Appointments / Agenda
                            </a>

                            <a href="{{ route('vehicles.index') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request()->routeIs('vehicles.*') ? 'bg-rose-50 text-[#E11D48]' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                Veículos
                            </a>

                            <a href="{{ route('maintenances.index') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request()->routeIs('maintenances.*') ? 'bg-rose-50 text-[#E11D48]' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                Manutenções
                            </a>

                            <a href="{{ route('fuels.index') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request()->routeIs('fuels.*') ? 'bg-rose-50 text-[#E11D48]' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                Abastecimento
                            </a>

                            <a href="{{ route('obligations.index') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request()->routeIs('obligations.*') ? 'bg-rose-50 text-[#E11D48]' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                IPVA & Taxas
                            </a>

                            <a href="{{ route('reports.index') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request()->routeIs('reports.*') ? 'bg-rose-50 text-[#E11D48]' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                Relatórios
                            </a>
                        </nav>
                    </div>

                    <!-- Right: Quick Action & User Menu -->
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('maintenances.create') }}" 
                           class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-medium rounded-lg shadow-sm transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Nova Manutenção</span>
                        </a>

                        <!-- User Profile Badge -->
                        <div class="flex items-center gap-2 pl-1 sm:pl-2">
                            <div class="w-7 h-7 rounded-full bg-gray-100 border border-gray-200 text-gray-700 font-semibold text-xs flex items-center justify-center shrink-0">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:inline-block text-xs font-medium text-gray-700 max-w-[120px] truncate whitespace-nowrap">
                                {{ Auth::user()->name }}
                            </span>

                            <!-- Logout Form -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" title="Sair da Conta" class="p-1 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Mobile Navigation Bar (Scrollable) -->
            <nav class="lg:hidden flex items-center gap-2 overflow-x-auto px-4 py-2.5 bg-gray-50 border-t border-gray-100 scrollbar-none text-xs font-medium">
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-white text-[#E11D48] shadow-sm font-semibold' : 'text-gray-600' }}">
                    Agenda
                </a>
                <a href="{{ route('vehicles.index') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('vehicles.*') ? 'bg-white text-[#E11D48] shadow-sm font-semibold' : 'text-gray-600' }}">
                    Veículos
                </a>
                <a href="{{ route('maintenances.index') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('maintenances.*') ? 'bg-white text-[#E11D48] shadow-sm font-semibold' : 'text-gray-600' }}">
                    Manutenções
                </a>
                <a href="{{ route('fuels.index') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('fuels.*') ? 'bg-white text-[#E11D48] shadow-sm font-semibold' : 'text-gray-600' }}">
                    Abastecimentos
                </a>
                <a href="{{ route('obligations.index') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('obligations.*') ? 'bg-white text-[#E11D48] shadow-sm font-semibold' : 'text-gray-600' }}">
                    IPVA
                </a>
                <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('reports.*') ? 'bg-white text-[#E11D48] shadow-sm font-semibold' : 'text-gray-600' }}">
                    Relatórios
                </a>
            </nav>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            
            <!-- Flash Alerts -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-white border border-emerald-200 text-emerald-800 text-sm shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-xl bg-white border border-rose-200 text-rose-800 text-sm shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-[#E11D48]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-white border border-rose-200 text-rose-800 text-sm shadow-sm flex items-start justify-between">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-[#E11D48] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <span class="font-semibold block">Atenção aos seguintes pontos:</span>
                            <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-rose-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endif

            <main>
                {{ $slot }}
            </main>

        </div>

    </body>
</html>
