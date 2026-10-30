<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-[#F9FAFB]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AutoCare - Gestão Preventiva e Telemetria Veicular</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-[#F9FAFB] text-gray-800 flex flex-col justify-between">

    <!-- Top Navigation Header -->
    <header class="border-b border-gray-200/80 bg-white/95 backdrop-blur-md sticky top-0 z-50 px-4 sm:px-8 py-3.5">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            
            <!-- Brand Identification -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-900 flex items-center justify-center text-white shadow-sm">
                    <svg class="w-5 h-5 text-[#E11D48]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="1.75"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 12l3.5-3.5"/>
                        <path stroke-linecap="round" stroke-width="2" d="M12 7v1M7 12h1M17 12h-1M8.5 8.5l.7.7M15.5 8.5l-.7.7"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-base tracking-tight text-gray-900">
                        Auto<span class="text-[#E11D48]">Care</span>
                    </span>
                    <span class="hidden sm:inline-block text-[11px] font-medium text-gray-400 ml-2 border-l border-gray-200 pl-2">
                        Gestão Preventiva Veicular
                    </span>
                </div>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex items-center gap-3 text-xs font-semibold">
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="px-4 sm:px-5 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white rounded-xl shadow-sm transition-all flex items-center gap-1.5">
                        <span>Acessar Painel</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-3.5 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-4 sm:px-5 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white rounded-xl shadow-sm transition-all">
                        Criar Garagem
                    </a>
                @endauth
            </div>

        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-8 py-8 sm:py-12 w-full space-y-12">
        
        <!-- Hero Section -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="h-1 bg-[#E11D48] w-full"></div>

            <div class="p-8 sm:p-12 lg:p-16 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- Left Column: Copy & Actions -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-rose-50 text-[#E11D48] border border-rose-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#E11D48]"></span>
                        <span>Plataforma de Manutenção Preventiva & Telemetria</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        Elimine o esquecimento de revisões com <span class="text-[#E11D48]">telemetria clara</span>.
                    </h1>

                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed max-w-xl">
                        Plano preventivo completo para carros e motocicletas. Projeção inteligente de odômetro diário, cronograma de trocas de óleo, filtros, correias e suspensão, telemetria de consumo (Km/L) e laudo valorizado para revenda.
                    </p>

                    <!-- CTA Action Group -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="{{ route('login') }}" 
                           class="px-6 py-3.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-semibold text-sm rounded-xl shadow-sm transition-all flex items-center gap-2">
                            <span>Acessar Painel de Controle</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>

                        <a href="{{ route('register') }}" 
                           class="px-6 py-3.5 bg-white hover:bg-gray-50 text-gray-800 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition-colors">
                            Cadastrar Veículo
                        </a>
                    </div>

                    <!-- Demo Credentials Box -->
                    <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 text-xs space-y-2">
                        <div class="flex items-center justify-between text-gray-500 font-medium">
                            <span>Acesso de Demonstração Pré-Carregado:</span>
                            <span class="text-[#E11D48] font-semibold">1 Clique</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-gray-700">
                            <div>Email: <strong class="bg-white px-2 py-0.5 rounded-md border border-gray-200 text-gray-900">demo@veiculos.com</strong></div>
                            <div>Senha: <strong class="bg-white px-2 py-0.5 rounded-md border border-gray-200 text-gray-900">password</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Clean Metric Highlights Card -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-gray-50/70 border border-gray-200/80 rounded-2xl p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Garagem Ativa</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Em Operação
                            </span>
                        </div>

                        <!-- Card Preview Item 1 -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900">Onix Plus Turbo</span>
                                <span class="text-xs font-semibold text-emerald-600">Em Dia</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Odômetro: 42.150 KM</span>
                                <span>Consumo: 13.8 KM/L</span>
                            </div>
                            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-[#E11D48] h-full rounded-full" style="width: 78%"></div>
                            </div>
                        </div>

                        <!-- Card Preview Item 2 -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900">Corolla Cross XRE</span>
                                <span class="text-xs font-semibold text-amber-600">Revisar em 1.500 KM</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Odômetro: 28.400 KM</span>
                                <span>Consumo: 12.2 KM/L</span>
                            </div>
                            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full" style="width: 90%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 text-xs text-gray-500">
                            <span>Histórico auditado com comprovantes</span>
                            <a href="{{ route('login') }}" class="text-[#E11D48] font-semibold hover:underline">Ver Prontuário &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Section 2: 4 Feature Pillars -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-gray-300 transition-all">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-[#E11D48] flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">
                    Cronograma Duplo (KM & Tempo)
                </h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                    Previsão dupla: por quilometragem (ex.: 10.000 km) ou tempo (ex.: 12 meses), alertando com margem de segurança antes do vencimento.
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-gray-300 transition-all">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">
                    Telemetria de Consumo (Km/L)
                </h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                    Registro rápido tanque a tanque com cálculo automático de média ponderada, custo por quilômetro rodado e histórico de postos.
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-gray-300 transition-all">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">
                    IPVA, Seguro & Licenciamento
                </h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                    Controle financeiro de obrigações anuais com cálculo de descontos em cota única, parcelamento e alerta de vencimento para evitar multas.
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-gray-300 transition-all">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">
                    Prontuário & Laudo de Venda
                </h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                    Emissão de Certificado de Manutenção Histórico em formato A4 impresso ou PDF, comprovando procedência mecânica e valorizando a revenda.
                </p>
            </div>

        </div>

        <!-- Section 3: Raio-X Mecânico dos Subsistemas (Clean Cards) -->
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200 pb-3">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                        Raio-X Mecânico dos Subsistemas Monitorados
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Diagramas técnicos e esquemas integrados aos alertas de manutenção
                    </p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-rose-50 text-[#E11D48] border border-rose-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#E11D48]"></span>
                    <span>Monitoramento Ativo</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: Suspensão -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-900">01. Suspensão & Direção</span>
                        <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-700 rounded-md">Geometria</span>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <img src="{{ asset('images/suspension-steering.png') }}" 
                             alt="Esquema de Suspensão e Direção" 
                             class="w-full h-40 object-contain mx-auto mix-blend-multiply">
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-600">Amortecedores & Molas</span>
                            <span class="font-semibold text-emerald-600">Em dia</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-600">Pivôs & Terminais</span>
                            <span class="font-semibold text-amber-600">Revisar em 4.000 KM</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-gray-600">Discos & Pastilhas</span>
                            <span class="font-semibold text-[#E11D48]">Substituir</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Diferencial -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-900">02. Câmbio & Diferencial</span>
                        <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-700 rounded-md">Transmissão</span>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <img src="{{ asset('images/differential-exploded.png') }}" 
                             alt="Esquema Diferencial Explodido" 
                             class="w-full h-40 object-contain mx-auto mix-blend-multiply">
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-600">Coroa & Pinhão</span>
                            <span class="font-semibold text-emerald-600">Ajustado</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-600">Óleo do Diferencial</span>
                            <span class="font-semibold text-amber-600">Troca em 8.000 KM</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-gray-600">Retentores & Semi-eixos</span>
                            <span class="font-semibold text-emerald-600">Sem vazamentos</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tração -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-900">03. Tração & Acoplamento</span>
                        <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-700 rounded-md">Cardan</span>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <img src="{{ asset('images/viscous-coupling.png') }}" 
                             alt="Acoplamento Viscoso Traseiro" 
                             class="w-full h-40 object-contain mx-auto mix-blend-multiply">
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-600">Discos Internos/Externos</span>
                            <span class="font-semibold text-emerald-600">Operacional</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-600">Árvore Cardan & Cruzeta</span>
                            <span class="font-semibold text-emerald-600">Lubrificado</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-gray-600">Molas & Batentes</span>
                            <span class="font-semibold text-amber-600">Conferir pressão</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

</body>
</html>
