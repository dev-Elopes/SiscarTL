<x-app-layout>

    <!-- Barra de Filtros & Ações Rápidas (Clean SaaS Style) -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 sm:p-5 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">

            <!-- Left: Dropdown Filters -->
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto text-sm">

                <!-- Vehicle Selector -->
                <div class="w-full sm:w-auto">
                    <select
                        onchange="window.location.href = '{{ route('dashboard') }}' + (this.value ? '?vehicle_id=' + this.value : '')"
                        class="w-full sm:w-auto bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15 shadow-sm">
                        <option value="">Todos os Veículos</option>
                        @foreach ($vehicles as $v)
                            <option value="{{ $v->id }}"
                                {{ $selectedVehicle && $selectedVehicle->id === $v->id ? 'selected' : '' }}>
                                [{{ $v->type === 'car' ? 'Carro' : 'Moto' }}] {{ $v->nickname }} ({{ $v->brand }}
                                {{ $v->model }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subsystem Selector -->
                <div class="w-full sm:w-auto">
                    <select
                        class="w-full sm:w-auto bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15 shadow-sm">
                        <option value="">Todos os Subsistemas</option>
                        <option value="engine_oil">Motor & Lubrificação</option>
                        <option value="transmission">Transmissão & Diferencial</option>
                        <option value="suspension_steering">Suspensão & Direção</option>
                        <option value="brakes">Sistema de Freios</option>
                        <option value="cooling_climate">Arrefecimento & Clima</option>
                    </select>
                </div>

            </div>

            <!-- Right: Checkboxes & Primary Action Button -->
            <div
                class="flex flex-wrap items-center gap-4 w-full lg:w-auto justify-between lg:justify-end text-sm text-gray-600">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20">
                    <span class="text-xs text-gray-600">Ocultar realizadas</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" checked
                        class="w-4 h-4 rounded border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20">
                    <span class="text-xs text-gray-600">Mostrar todos</span>
                </label>

                <a href="{{ route('maintenances.create') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-medium rounded-lg shadow-sm transition-all whitespace-nowrap shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Nova Manutenção</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Barra de Odômetro Rápido (Se houver veículo selecionado) -->
    @if ($selectedVehicle)
        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm mb-6">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4">

                <!-- Identificação do Veículo -->
                <div class="flex items-center gap-3.5 w-full lg:w-auto">
                    <div
                        class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-700 shrink-0">
                        @if ($selectedVehicle->type === 'car')
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM3 13l2-7h14l2 7M3 13h18M3 13v5a1 1 0 001 1h1m16-6v5a1 1 0 01-1 1h-1" />
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-base text-gray-900">{{ $selectedVehicle->nickname }}</span>
                            <span
                                class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded border border-gray-200">
                                {{ $selectedVehicle->license_plate ?? 'Sem Placa' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $selectedVehicle->brand }} {{ $selectedVehicle->model }} •
                                {{ $selectedVehicle->year_manufacture }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-3">
                            <span>Odômetro atual: <strong
                                    class="text-gray-900 font-semibold">{{ number_format($selectedVehicle->current_mileage, 0, ',', '.') }}
                                    km</strong></span>
                            <span>•</span>
                            <span>Média de uso: <strong
                                    class="text-gray-700 font-semibold">{{ $selectedVehicle->average_daily_km }}
                                    km/dia</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Formulário de Atualização Rápida de KM com Chips -->
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-end">
                    <form action="{{ route('vehicles.odometer.update', $selectedVehicle->id) }}" method="POST"
                        class="flex flex-wrap items-center gap-2" x-data="{ km: {{ $selectedVehicle->current_mileage }} }">
                        @csrf
                        <input type="hidden" name="recorded_at" value="{{ date('Y-m-d') }}">

                        <span class="text-xs font-medium text-gray-500">Atualizar KM:</span>

                        <!-- Chips de 1 clique -->
                        <div class="flex items-center gap-1">
                            <button type="button" @click="km = km + 100"
                                class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-medium transition-colors">
                                +100 km
                            </button>
                            <button type="button" @click="km = km + 500"
                                class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-medium transition-colors">
                                +500 km
                            </button>
                            <button type="button" @click="km = km + 1000"
                                class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-medium transition-colors">
                                +1.000 km
                            </button>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <input type="number" name="mileage" x-model="km"
                                min="{{ $selectedVehicle->current_mileage }}"
                                class="w-28 px-3 py-1.5 bg-white border border-[#D1D5DB] rounded-lg text-xs font-semibold text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48]">

                            <button type="submit"
                                class="px-3.5 py-1.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-medium text-xs rounded-lg transition-colors shadow-sm">
                                Salvar
                            </button>
                        </div>
                    </form>

                    <!-- Botão de Simulação de Dados de Demonstração (1 Clique) -->
                    @if (app()->environment(['local', 'testing']))
                        <form method="POST" action="{{ route('vehicles.simulate', $selectedVehicle->id) }}"
                            class="inline">
                            @csrf
                            <button type="submit"
                                title="Preenche automaticamente revisões anteriores, abastecimentos e IPVA para teste"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-[#E11D48] border border-rose-200 rounded-lg text-xs font-semibold transition-colors shadow-sm whitespace-nowrap">
                                <span>🎲 Preencher Teste</span>
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    @endif

    <!-- 4 Indicadores Principais (KPIs em Cards Limpos) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <!-- Indicador 1: Revisões Vencidas -->
        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 block">Revisões Vencidas</span>
                <div class="text-2xl font-bold text-gray-900 mt-1">
                    {{ count($overdueSchedules) }}
                </div>
                <span class="text-xs font-medium text-[#E11D48] mt-0.5 block">Requer atenção imediata</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-[#E11D48] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <!-- Indicador 2: Em Atenção -->
        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 block">Próximas do Prazo</span>
                <div class="text-2xl font-bold text-gray-900 mt-1">
                    {{ count($warningSchedules) }}
                </div>
                <span class="text-xs text-amber-600 font-medium mt-0.5 block">&lt; 2.000 km ou 30 dias</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Indicador 3: Sistemas em Dia -->
        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 block">Sistemas em Dia</span>
                <div class="text-2xl font-bold text-gray-900 mt-1">
                    {{ count($okSchedules) }}
                </div>
                <span class="text-xs text-emerald-600 font-medium mt-0.5 block">Manutenções regulares</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Indicador 4: Custo do Mês -->
        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 block">Despesas deste Mês</span>
                <div class="text-2xl font-bold text-gray-900 mt-1">
                    R$ {{ number_format($totalMonthlyCost, 2, ',', '.') }}
                </div>
                <span class="text-xs text-gray-500 mt-0.5 block">Manutenção + Combustível</span>
            </div>
            <div
                class="w-10 h-10 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center font-bold text-sm">
                R$
            </div>
        </div>

    </div>

    <!-- Área Principal com Alternador de Visualização (Tabela Appointments vs Esquemas Técnicos) -->
    <div x-data="{ viewMode: 'appointments', tab: '{{ (count($overdueSchedules) + count($warningSchedules) > 0) ? 'urgent' : 'all' }}' }" class="space-y-6">

        <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden">
            
            <!-- Header do Bloco Principal -->
            <div class="p-5 sm:p-6 border-b border-[#E5E7EB] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">
                        Appointments & Cronograma de Revisões
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Acompanhe os prazos programados de cada item preventivo por quilometragem e tempo.
                    </p>
                </div>

                <!-- Tabs de Modo de Visualização -->
                <div class="flex flex-wrap items-center gap-2 self-start sm:self-center">
                    <!-- Sub-abas de Prazos -->
                    <div x-show="viewMode === 'appointments'" class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl text-xs">
                        <button type="button" @click="tab = 'urgent'" 
                                :class="tab === 'urgent' ? 'bg-white text-[#E11D48] shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                            <span>Próximas & Atenção</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ count($overdueSchedules) + count($warningSchedules) > 0 ? 'bg-rose-100 text-[#E11D48]' : 'bg-gray-200 text-gray-600' }}">
                                {{ count($overdueSchedules) + count($warningSchedules) }}
                            </span>
                        </button>
                        <button type="button" @click="tab = 'all'" 
                                :class="tab === 'all' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                            <span>Todas as Revisões</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-gray-200 text-gray-600">
                                {{ count($allSchedules) }}
                            </span>
                        </button>
                    </div>

                    <!-- Alternador de Visão (Tabela vs Esquemas) -->
                    <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl text-xs">
                        <button @click="viewMode = 'appointments'" 
                                :class="viewMode === 'appointments' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-lg transition-all">
                            Tabela de Prazos
                        </button>

                        <button @click="viewMode = 'diagrams'" 
                                :class="viewMode === 'diagrams' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-lg transition-all">
                            Esquemas Mecânicos
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabela de Agendamentos / Appointments -->
            <div x-show="viewMode === 'appointments'">
                
                <!-- Card Tranquilizador quando tudo está em dia na aba Próximas -->
                <div x-show="tab === 'urgent' && {{ count($overdueSchedules) + count($warningSchedules) === 0 ? 'true' : 'false' }}" 
                     class="p-8 text-center bg-emerald-50/50 border-b border-emerald-100">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center mb-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Tudo 100% em dia com seu veículo!</h3>
                    <p class="text-xs text-gray-600 mt-1 max-w-md mx-auto">Nenhuma revisão preventiva está vencida ou próxima do prazo. Todos os sistemas estão operando dentro da margem de segurança.</p>
                    <button type="button" @click="tab = 'all'" class="mt-3.5 inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-gray-200 hover:border-gray-300 text-gray-800 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                        <span>Ver todas as {{ count($allSchedules) }} revisões programadas &rarr;</span>
                    </button>
                </div>

                <div class="overflow-x-auto" x-show="tab === 'all' || {{ count($overdueSchedules) + count($warningSchedules) > 0 ? 'true' : 'false' }}">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-gray-50/75 border-b border-gray-200 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4 w-12 text-center">#</th>
                                <th class="py-3.5 px-4">Previsão (KM / Data)</th>
                                <th class="py-3.5 px-4">Veículo</th>
                                <th class="py-3.5 px-4">Subsistema</th>
                                <th class="py-3.5 px-4">Item Preventivo</th>
                                <th class="py-3.5 px-4">Situação</th>
                                <th class="py-3.5 px-4 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse ($allSchedules as $index => $schedule)
                                @php
                                    $isOverdue = $schedule->status === 'overdue';
                                    $isWarning = $schedule->status === 'warning';
                                    $diffKm = $schedule->next_due_km ? ($schedule->next_due_km - ($schedule->vehicle->current_mileage ?? 0)) : null;
                                @endphp
                                <tr x-show="tab === 'all' || (tab === 'urgent' && {{ $isOverdue || $isWarning ? 'true' : 'false' }})" 
                                    class="hover:bg-gray-50/80 transition-colors {{ $isOverdue ? 'bg-rose-50/20' : '' }}">
                                    
                                    <!-- Index -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="text-xs text-gray-400 font-medium">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    </td>

                                    <!-- Vencimento -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">
                                            {{ $schedule->next_due_km ? number_format($schedule->next_due_km, 0, ',', '.') . ' km' : '---' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $schedule->next_due_date ? date('d/m/Y', strtotime($schedule->next_due_date)) : 'Sem limite temporal' }}
                                        </div>
                                    </td>

                                    <!-- Veículo -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900 flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            <span>{{ $schedule->vehicle->nickname }}</span>
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $schedule->vehicle->license_plate ?? 'Sem Placa' }} • {{ $schedule->vehicle->brand }}
                                        </div>
                                    </td>

                                    <!-- Subsistema -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-block bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md text-[11px] font-medium">
                                            {{ match($schedule->system_category) {
                                                'engine_oil' => 'Motor & Óleo',
                                                'transmission' => 'Câmbio & Diferencial',
                                                'suspension_steering' => 'Suspensão & Direção',
                                                'brakes' => 'Freios',
                                                'cooling_climate' => 'Arrefecimento',
                                                default => 'Geral'
                                            } }}
                                        </span>
                                    </td>

                                    <!-- Item Preventivo -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-900">{{ $schedule->item_name }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            A cada {{ number_format($schedule->interval_km, 0, ',', '.') }} km ou {{ $schedule->interval_months }} meses
                                        </div>
                                    </td>

                                    <!-- Status Pill -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($isOverdue)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-[#E11D48] border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#E11D48]"></span>
                                                <span>Vencido ({{ $diffKm !== null ? number_format(abs($diffKm), 0, ',', '.') . ' km atrás' : 'Prazo excedido' }})</span>
                                            </span>
                                        @elseif ($isWarning)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span>Atenção (restam {{ $diffKm !== null ? number_format($diffKm, 0, ',', '.') . ' km' : 'poucos dias' }})</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                <span>Em dia (restam {{ $diffKm !== null ? number_format($diffKm, 0, ',', '.') . ' km' : 'ok' }})</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Ação Rápida -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <a href="{{ route('maintenances.create') }}?vehicle_id={{ $schedule->vehicle_id }}&item_name={{ urlencode($schedule->item_name) }}" 
                                           class="inline-block px-3 py-1.5 bg-gray-50 hover:bg-rose-50 text-gray-700 hover:text-[#E11D48] border border-gray-200 hover:border-rose-200 rounded-lg text-xs font-medium transition-all">
                                            Dar Baixa
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-gray-400 text-xs">
                                        Nenhum plano de manutenção ativo encontrado para o filtro selecionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Visualização dos Esquemas Mecânicos -->
            <div x-show="viewMode === 'diagrams'" class="p-6 space-y-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Diagrama 1: Suspensão Dianteira & Direção -->
                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-xs text-gray-800 uppercase tracking-wider">Suspensão Dianteira & Direção</span>
                            <span class="text-xs text-gray-500 font-medium">Esquema Técnico</span>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-2 mb-3">
                            <img src="{{ asset('images/suspension-steering.png') }}" alt="Suspensão" class="w-full h-56 object-contain mx-auto">
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2 bg-white rounded border border-gray-100 flex justify-between">
                                <span class="text-gray-600">Amortecedores:</span>
                                <span class="text-emerald-600 font-semibold">Em dia</span>
                            </div>
                            <div class="p-2 bg-white rounded border border-gray-100 flex justify-between">
                                <span class="text-gray-600">Pivôs & Terminais:</span>
                                <span class="text-amber-600 font-semibold">Revisar em breve</span>
                            </div>
                        </div>
                    </div>

                    <!-- Diagrama 2: Diferencial Traseiro -->
                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-xs text-gray-800 uppercase tracking-wider">Diferencial & Transmissão</span>
                            <span class="text-xs text-gray-500 font-medium">Vista Explodida</span>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-2 mb-3">
                            <img src="{{ asset('images/differential-exploded.png') }}" alt="Diferencial" class="w-full h-56 object-contain mx-auto">
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2 bg-white rounded border border-gray-100 flex justify-between">
                                <span class="text-gray-600">Coroa & Pinhão:</span>
                                <span class="text-emerald-600 font-semibold">Ajustado</span>
                            </div>
                            <div class="p-2 bg-white rounded border border-gray-100 flex justify-between">
                                <span class="text-gray-600">Óleo Hipóide:</span>
                                <span class="text-amber-600 font-semibold">Conferir nível</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</x-app-layout>
