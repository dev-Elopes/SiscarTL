<x-app-layout>

    <!-- Top Navigation Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#E11D48]"></span>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    BI & Telemetria de Abastecimento
                </h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Análise de volume de litros, custo por quilômetro rodado e projeção preditiva de consumo
            </p>
        </div>

        <!-- Seletor Rápido de Veículo para o BI -->
        <div class="flex items-center gap-2.5">
            <form method="GET" action="{{ route('fuels.index') }}" class="flex items-center gap-2">
                <label for="vehicle_filter" class="text-xs font-medium text-gray-600">Filtrar:</label>
                <select id="vehicle_filter" name="vehicle_id" onchange="this.form.submit()" 
                        class="bg-white border border-gray-200 text-xs font-semibold text-gray-800 rounded-xl px-3 py-1.5 focus:outline-none focus:border-[#E11D48] shadow-sm">
                    <option value="">Todos os Veículos da Garagem</option>
                    @foreach ($vehicles as $v)
                        <option value="{{ $v->id }}" {{ $selectedVehicleId == $v->id ? 'selected' : '' }}>
                            {{ $v->nickname }} ({{ $v->license_plate ?? 'S/ Placa' }})
                        </option>
                    @endforeach
                </select>
            </form>
            @if ($selectedVehicle)
                <a href="{{ route('vehicles.edit', $selectedVehicle) }}" class="text-xs text-gray-500 hover:text-[#E11D48] transition-colors p-1.5 bg-white border border-gray-200 rounded-lg shadow-sm" title="Editar consumo deste veículo">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
            @endif
        </div>
    </div>

    <!-- 4 Indicadores Principais de BI (KPI Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- KPI 1: Gasto Total -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total em Combustível</span>
                <span class="p-2 bg-rose-50 text-[#E11D48] rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-900 tracking-tight">
                R$ {{ number_format($totalSpent, 2, ',', '.') }}
            </div>
            <span class="text-xs text-gray-400 mt-1 block">despesas acumuladas</span>
        </div>

        <!-- KPI 2: Volume Total de Litros -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Volume Total Injetado</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-900 tracking-tight">
                {{ number_format($totalLiters, 2, ',', '.') }} <span class="text-base font-medium text-gray-500">L</span>
            </div>
            <span class="text-xs text-gray-400 mt-1 block">litros abastecidos na bomba</span>
        </div>

        <!-- KPI 3: Rendimento Médio (Km/L) -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rendimento Médio</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold text-emerald-600 tracking-tight">
                {{ $avgKmLiter ? number_format($avgKmLiter, 1, ',', '.') : number_format($expectedConsumption, 1, ',', '.') }} <span class="text-base font-medium text-emerald-700">KM/L</span>
            </div>
            <div class="text-[11px] text-gray-500 mt-1 flex items-center justify-between">
                <span>{{ $avgKmLiter ? 'Aferido na bomba' : 'Estimado pelo veículo' }}</span>
                @if ($selectedVehicle?->expected_km_per_liter)
                    <span class="text-gray-400">Meta: {{ number_format($selectedVehicle->expected_km_per_liter, 1, ',', '.') }}</span>
                @endif
            </div>
        </div>

        <!-- KPI 4: Custo por KM & Previsão Mensal -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Custo / KM Rodado</span>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold text-purple-700 tracking-tight">
                R$ {{ number_format($costPerKm, 2, ',', '.') }} <span class="text-xs font-medium text-gray-500">/ km</span>
            </div>
            <span class="text-xs text-gray-500 mt-1 block">
                Previsão Mensal: <strong class="text-gray-800">R$ {{ number_format($estimatedMonthlyExpense, 2, ',', '.') }}</strong>
            </span>
        </div>

    </div>

    <!-- Seção BI de Gráficos (Charts) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Gráfico 1: Volume de Litros & Despesas Mensais -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Evolução Mensal de Abastecimentos</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Litros abastecidos e valor total investido por mês</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 text-gray-600 font-medium">
                        <span class="w-3 h-3 rounded bg-blue-500"></span> Litros
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-gray-600 font-medium">
                        <span class="w-3 h-3 rounded bg-[#E11D48]"></span> Gasto (R$)
                    </span>
                </div>
            </div>

            <div class="relative h-64 w-full">
                <canvas id="monthlyFuelChart"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Histórico de Rendimento (Km/L) e Preço da Gasolina -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Histórico de Rendimento & Preço</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Variação do preço na bomba vs consumo real aferido</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 text-gray-600 font-medium">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Km/L
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-gray-600 font-medium">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span> R$/Litro
                    </span>
                </div>
            </div>

            <div class="relative h-64 w-full">
                <canvas id="efficiencyFuelChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Seção de Ações: Formulário de Entrada Inteligente + Tabela de Histórico -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Formulário de Abastecimento com Calculadora de Projeção em Tempo Real (4 cols) -->
        <div class="lg:col-span-4">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden sticky top-20">
                <div class="h-1 bg-[#E11D48] w-full"></div>

                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Registrar Abastecimento</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Calcula autonomia e gasto em tempo real</p>
                        </div>
                        <span class="p-1.5 bg-gray-50 text-gray-500 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"/></svg>
                        </span>
                    </div>

                    @php
                        // Dados do veículo selecionado para carregar no Alpine
                        $defaultKmPerLiter = $selectedVehicle?->expected_km_per_liter ?: ($avgKmLiter ?: 12.0);
                        $defaultTank = $selectedVehicle?->tank_capacity ?: 45;
                        $defaultMonthlyKm = $selectedVehicle?->average_monthly_mileage ?: 1000;
                    @endphp

                    <form method="POST" action="{{ route('fuels.store') }}" class="space-y-4" 
                          x-data="{
                              liters: '',
                              price: '',
                              expectedKmL: {{ $defaultKmPerLiter }},
                              monthlyKm: {{ $defaultMonthlyKm }},
                              get totalCost() {
                                  const l = parseFloat(this.liters) || 0;
                                  const p = parseFloat(this.price) || 0;
                                  return (l * p);
                              },
                              get estimatedKm() {
                                  const l = parseFloat(this.liters) || 0;
                                  return Math.round(l * (parseFloat(this.expectedKmL) || 12));
                              },
                              get costPerKm() {
                                  const p = parseFloat(this.price) || 0;
                                  const kml = parseFloat(this.expectedKmL) || 12;
                                  return kml > 0 ? (p / kml) : 0;
                              },
                              get monthlyExpense() {
                                  return (this.costPerKm * this.monthlyKm);
                              }
                          }">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Veículo</label>
                            <select name="vehicle_id" required 
                                    class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                                @foreach ($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ $selectedVehicleId == $v->id ? 'selected' : '' }}>
                                        {{ $v->nickname }} ({{ $v->license_plate ?? 'S/ Placa' }}) - {{ $v->expected_km_per_liter ? number_format($v->expected_km_per_liter, 1, ',', '.') . ' km/L' : 'sem km/L config.' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Data</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                       class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Odômetro (KM)</label>
                                <input type="number" name="mileage" value="{{ $selectedVehicle?->current_mileage ? $selectedVehicle->current_mileage + 350 : '' }}" required placeholder="Ex: 96500"
                                       class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Combustível</label>
                            <select name="fuel_type" required class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                                <option value="gasoline">Gasolina Comum / Aditivada</option>
                                <option value="ethanol">Etanol</option>
                                <option value="diesel">Diesel S10 / Comum</option>
                                <option value="cng">GNV</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Litros Injetados</label>
                                <input type="number" step="0.01" name="liters" x-model="liters" required placeholder="0,00"
                                       class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Preço/Litro (R$)</label>
                                <input type="number" step="0.01" name="price_per_liter" x-model="price" required placeholder="Ex: 5.89"
                                       class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                            </div>
                        </div>

                        <!-- Card de Projeção em Tempo Real (Lógica Solicitada pelo Usuário) -->
                        <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-xl space-y-2 text-xs">
                            <div class="flex items-center justify-between pb-1.5 border-b border-gray-200">
                                <span class="text-gray-500 font-medium">Total a Pagar:</span>
                                <span class="font-bold text-gray-900 text-sm">
                                    R$ <span x-text="totalCost.toFixed(2).replace('.', ',')"></span>
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-gray-600">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    Autonomia Estimada:
                                </span>
                                <span class="font-bold text-emerald-700">
                                    ~<span x-text="estimatedKm"></span> km
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-gray-600">
                                <span>Custo por KM rodado:</span>
                                <span class="font-semibold text-gray-900">
                                    R$ <span x-text="costPerKm.toFixed(2).replace('.', ',')"></span>/km
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-gray-600 pt-1 border-t border-gray-200 text-[11px]">
                                <span>Previsão de gasto no mês:</span>
                                <span class="font-semibold text-gray-800">
                                    ~R$ <span x-text="monthlyExpense.toFixed(2).replace('.', ',')"></span>
                                </span>
                            </div>
                        </div>

                        <label class="flex items-center gap-2.5 cursor-pointer select-none text-xs text-gray-700">
                            <input type="checkbox" name="is_full_tank" id="is_full_tank" value="1" checked 
                                   class="w-4 h-4 rounded text-[#E11D48] focus:ring-0 focus:ring-offset-0 border-gray-300">
                            <span class="font-medium">Tanque Cheio? (Permite calibrar o consumo real exato)</span>
                        </label>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Posto / Bandeira (Opcional)</label>
                            <input type="text" name="gas_station" placeholder="Ex: Shell, Ipiranga, Petrobras"
                                   class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                        </div>

                        <button type="submit" 
                                class="w-full py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-semibold text-xs rounded-xl shadow-sm transition-colors flex items-center justify-center gap-1.5">
                            <span>Gravar Abastecimento</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Histórico de Abastecimentos (8 cols) -->
        <div class="lg:col-span-8">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Registros de Abastecimento</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Histórico consolidado com cálculo de consumo aferido</p>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">
                        {{ $records->total() }} registros
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-semibold uppercase">
                            <tr>
                                <th class="py-3 px-4">Data & Odômetro</th>
                                <th class="py-3 px-4">Veículo</th>
                                <th class="py-3 px-4">Combustível</th>
                                <th class="py-3 px-4">Volume & Preço</th>
                                <th class="py-3 px-4">Total Pago</th>
                                <th class="py-3 px-4 text-center">Consumo (Km/L)</th>
                                <th class="py-3 px-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse ($records as $r)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="font-semibold text-gray-900 block">{{ date('d/m/Y', strtotime($r->date)) }}</span>
                                        <span class="text-xs text-gray-400">{{ number_format($r->mileage, 0, ',', '.') }} KM</span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="font-medium text-gray-900">{{ $r->vehicle->nickname }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="font-medium text-gray-800 block">{{ $r->fuel_label }}</span>
                                        <span class="text-xs text-gray-400">{{ $r->gas_station ?? '-' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="font-medium text-gray-900">{{ number_format($r->liters, 2, ',', '.') }} L</span>
                                        <span class="block text-xs text-gray-400">@ R$ {{ number_format($r->price_per_liter, 2, ',', '.') }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-gray-900">
                                        R$ {{ number_format($r->total_cost, 2, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if ($r->km_per_liter)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                {{ number_format($r->km_per_liter, 1, ',', '.') }} KM/L
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400" title="Necessário abastecer tanque cheio duas vezes seguidas para cálculo">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <form method="POST" action="{{ route('fuels.destroy', $r) }}" onsubmit="return confirm('Excluir este registro de abastecimento?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-rose-600 transition-colors p-1" title="Excluir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400 text-xs">
                                        Nenhum registro de abastecimento cadastrado para o filtro selecionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($records->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Script de Inicialização dos Gráficos com Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dados vindos do Controller
            const labels = @json($chartLabels);
            const litersData = @json($chartLiters);
            const costsData = @json($chartCosts);

            const effLabels = @json($efficiencyLabels);
            const effKmL = @json($efficiencyKmPerLiter);
            const effPrice = @json($efficiencyPrice);

            // Gráfico 1: Volume & Gastos Mensais
            const ctxMonthly = document.getElementById('monthlyFuelChart');
            if (ctxMonthly) {
                new Chart(ctxMonthly, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Volume (Litros)',
                                data: litersData,
                                backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1,
                                borderRadius: 6,
                                yAxisID: 'yLiters'
                            },
                            {
                                label: 'Despesas (R$)',
                                data: costsData,
                                type: 'line',
                                borderColor: 'rgb(225, 29, 72)',
                                backgroundColor: 'rgba(225, 29, 72, 0.1)',
                                tension: 0.35,
                                fill: true,
                                borderWidth: 2,
                                pointRadius: 4,
                                pointBackgroundColor: 'rgb(225, 29, 72)',
                                yAxisID: 'yCost'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 11 }, color: '#6B7280' }
                            },
                            yLiters: {
                                type: 'linear',
                                position: 'left',
                                grid: { color: '#F3F4F6' },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#6B7280',
                                    callback: function(value) { return value + ' L'; }
                                }
                            },
                            yCost: {
                                type: 'linear',
                                position: 'right',
                                grid: { display: false },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#E11D48',
                                    callback: function(value) { return 'R$ ' + value; }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // Gráfico 2: Rendimento & Preço
            const ctxEff = document.getElementById('efficiencyFuelChart');
            if (ctxEff) {
                // Fallback para quando há poucos dados históricos
                const displayLabels = effLabels.length > 0 ? effLabels : labels;
                const displayKmL = effLabels.length > 0 ? effKmL : labels.map(() => {{ $expectedConsumption }});
                const displayPrice = effLabels.length > 0 ? effPrice : labels.map(() => {{ $avgPricePerLiter }});

                new Chart(ctxEff, {
                    type: 'line',
                    data: {
                        labels: displayLabels,
                        datasets: [
                            {
                                label: 'Km / Litro',
                                data: displayKmL,
                                borderColor: 'rgb(16, 185, 129)',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.3,
                                fill: true,
                                borderWidth: 2,
                                pointRadius: 4,
                                pointBackgroundColor: 'rgb(16, 185, 129)',
                                yAxisID: 'yKm'
                            },
                            {
                                label: 'Preço/Litro (R$)',
                                data: displayPrice,
                                borderColor: 'rgb(245, 158, 11)',
                                backgroundColor: 'rgba(245, 158, 11, 0.05)',
                                tension: 0.3,
                                fill: false,
                                borderWidth: 2,
                                borderDash: [4, 4],
                                pointRadius: 4,
                                pointBackgroundColor: 'rgb(245, 158, 11)',
                                yAxisID: 'yPrice'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 11 }, color: '#6B7280' }
                            },
                            yKm: {
                                type: 'linear',
                                position: 'left',
                                grid: { color: '#F3F4F6' },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#10B981',
                                    callback: function(value) { return value + ' km/l'; }
                                }
                            },
                            yPrice: {
                                type: 'linear',
                                position: 'right',
                                grid: { display: false },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#F59E0B',
                                    callback: function(value) { return 'R$ ' + value.toFixed(2); }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>

</x-app-layout>
