<x-app-layout>

    <!-- Header do Prontuário do Veículo -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 sm:p-6 mb-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Identificação do Veículo -->
            <div class="flex items-start sm:items-center gap-3.5">
                <a href="{{ route('vehicles.index') }}" 
                   title="Voltar à Garagem"
                   class="p-2 border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>

                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                            {{ $vehicle->nickname }}
                        </h1>
                        <span class="bg-gray-100 text-gray-700 px-2 py-0.5 text-xs font-semibold rounded border border-gray-200">
                            {{ $vehicle->license_plate ?? 'Sem Placa' }}
                        </span>
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $vehicle->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600' }}">
                            {{ $vehicle->status === 'active' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $vehicle->brand }} {{ $vehicle->model }} • Fab/Mod: {{ $vehicle->year_manufacture }}/{{ $vehicle->year_model }} • Combustível: {{ $vehicle->fuel_type_label }} • Uso: {{ $vehicle->usage_severity === 'severe' ? 'Severo' : 'Normal' }}
                    </p>
                </div>
            </div>

            <!-- Botões de Ação Rápida -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('maintenances.create', ['vehicle_id' => $vehicle->id]) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-semibold rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Nova Manutenção</span>
                </a>

                <a href="{{ route('reports.certificate', $vehicle) }}" target="_blank" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200 shadow-sm transition-all">
                    <span>📄 Laudo de Venda (A4)</span>
                </a>

                <a href="{{ route('reports.export-csv', $vehicle) }}" 
                   class="px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-medium rounded-lg border border-gray-200 transition-colors">
                    CSV
                </a>
            </div>

        </div>
    </div>

    <!-- 4 Métricas do Veículo -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 text-xs">
        
        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-sm">
            <span class="text-xs font-medium text-gray-500 block">Odômetro Atual</span>
            <span class="text-xl font-bold text-gray-900 block mt-1">
                {{ number_format($vehicle->current_mileage, 0, ',', '.') }} km
            </span>
            <span class="text-[11px] text-[#E11D48] font-medium block mt-0.5">Est. hoje: ~{{ number_format($vehicle->estimated_mileage, 0, ',', '.') }} km</span>
        </div>

        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-sm">
            <span class="text-xs font-medium text-gray-500 block">Total em Manutenções</span>
            <span class="text-xl font-bold text-gray-900 block mt-1">
                R$ {{ number_format($totalMaintenance, 2, ',', '.') }}
            </span>
            <span class="text-[11px] text-gray-500 font-medium block mt-0.5">{{ $vehicle->maintenanceRecords->count() }} intervenções</span>
        </div>

        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-sm">
            <span class="text-xs font-medium text-gray-500 block">Consumo Médio</span>
            <span class="text-xl font-bold text-gray-900 block mt-1">
                {{ $avgKmLiter ? number_format($avgKmLiter, 1, ',', '.') : '-' }} km/l
            </span>
            <span class="text-[11px] text-gray-500 font-medium block mt-0.5">R$ {{ number_format($totalFuel, 2, ',', '.') }} abastecido</span>
        </div>

        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-sm">
            <span class="text-xs font-medium text-gray-500 block">Custo Total Acumulado</span>
            <span class="text-xl font-bold text-gray-900 block mt-1">
                R$ {{ number_format($grandTotal, 2, ',', '.') }}
            </span>
            <span class="text-[11px] text-gray-500 font-medium block mt-0.5">Manutenção + Combustível</span>
        </div>

    </div>

    <!-- Navegação em Abas (Tabs) -->
    <div x-data="{ activeTab: 'schedules' }" class="space-y-6">
        
        <!-- Tab Buttons Bar -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 border-b border-gray-200 scrollbar-none text-xs font-medium">
            <button @click="activeTab = 'schedules'" 
                    :class="activeTab === 'schedules' ? 'bg-rose-50 text-[#E11D48] font-semibold border-b-2 border-[#E11D48] rounded-t-lg' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2.5 transition-all whitespace-nowrap">
                Plano Preventivo & Prazos ({{ $vehicle->schedules->count() }})
            </button>

            <button @click="activeTab = 'records'" 
                    :class="activeTab === 'records' ? 'bg-rose-50 text-[#E11D48] font-semibold border-b-2 border-[#E11D48] rounded-t-lg' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2.5 transition-all whitespace-nowrap">
                Prontuário Histórico ({{ $vehicle->maintenanceRecords->count() }})
            </button>

            <button @click="activeTab = 'fuels'" 
                    :class="activeTab === 'fuels' ? 'bg-rose-50 text-[#E11D48] font-semibold border-b-2 border-[#E11D48] rounded-t-lg' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2.5 transition-all whitespace-nowrap">
                Abastecimentos ({{ $vehicle->fuelRecords->count() }})
            </button>

            <button @click="activeTab = 'obligations'" 
                    :class="activeTab === 'obligations' ? 'bg-rose-50 text-[#E11D48] font-semibold border-b-2 border-[#E11D48] rounded-t-lg' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2.5 transition-all whitespace-nowrap">
                IPVA & Taxas ({{ $vehicle->obligations->count() }})
            </button>

            <button @click="activeTab = 'odometer'" 
                    :class="activeTab === 'odometer' ? 'bg-rose-50 text-[#E11D48] font-semibold border-b-2 border-[#E11D48] rounded-t-lg' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2.5 transition-all whitespace-nowrap">
                Odômetro
            </button>
        </div>

        <!-- TAB 1: Plano Preventivo -->
        <div x-show="activeTab === 'schedules'" class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">
                        Cronograma de Plano Preventivo
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Intervalos de manutenção sugeridos com base no perfil: <strong class="text-gray-800">{{ $vehicle->usage_severity === 'severe' ? 'Uso Severo (Urbano)' : 'Uso Normal (Estrada)' }}</strong>
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-gray-50/75 border-b border-gray-200 text-[11px] text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="py-3 px-4">Item Preventivo</th>
                            <th class="py-3 px-4">Intervalo Padrão</th>
                            <th class="py-3 px-4">Última Realização</th>
                            <th class="py-3 px-4">Próximo Vencimento</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($vehicle->schedules as $sched)
                            <tr class="hover:bg-gray-50/60 transition-colors {{ $sched->status === 'overdue' ? 'bg-rose-50/20' : '' }}">
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-gray-900 block">{{ $sched->item_name }}</span>
                                    <span class="text-[11px] text-gray-400">{{ $sched->category_label }}</span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-800">{{ $sched->interval_km ? number_format($sched->interval_km, 0, ',', '.') . ' km' : '-' }}</span>
                                    <span class="text-gray-400 block text-[11px]">ou {{ $sched->interval_months ? $sched->interval_months . ' meses' : '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($sched->last_performed_at)
                                        <span class="font-semibold text-gray-800">{{ date('d/m/Y', strtotime($sched->last_performed_at)) }}</span>
                                        <span class="block text-gray-400 text-[11px]">aos {{ number_format($sched->last_performed_mileage, 0, ',', '.') }} km</span>
                                    @else
                                        <span class="text-gray-400 italic">Nunca registrado</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($sched->next_due_mileage)
                                        <span class="font-semibold text-gray-900">{{ number_format($sched->next_due_mileage, 0, ',', '.') }} km</span>
                                    @endif
                                    @if ($sched->next_due_date)
                                        <span class="block text-[11px] text-gray-400">em {{ date('d/m/Y', strtotime($sched->next_due_date)) }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if ($sched->status === 'overdue')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-[#E11D48] border border-rose-200">
                                            Vencido
                                        </span>
                                    @elseif ($sched->status === 'warning')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Atenção
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Em dia
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('maintenances.create', ['vehicle_id' => $vehicle->id, 'schedule_id' => $sched->id, 'type' => ($sched->system_category === 'engine_oil' ? 'oil_change' : 'preventive')]) }}" 
                                       class="px-3 py-1.5 bg-gray-50 hover:bg-rose-50 text-gray-700 hover:text-[#E11D48] border border-gray-200 hover:border-rose-200 rounded-lg text-xs font-medium transition-all">
                                        Dar Baixa
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: Prontuário Histórico -->
        <div x-show="activeTab === 'records'" style="display: none;" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Linha do Tempo de Intervenções</h3>
                <a href="{{ route('maintenances.create', ['vehicle_id' => $vehicle->id]) }}" 
                   class="px-3.5 py-2 bg-[#E11D48] hover:bg-[#BE123C] text-white font-medium text-xs rounded-lg shadow-sm">
                    + Adicionar Registro
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($vehicle->maintenanceRecords as $record)
                    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-gray-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                        {{ $record->type_label }}
                                    </span>
                                    <span class="text-xs text-gray-400">• {{ date('d/m/Y', strtotime($record->performed_at)) }}</span>
                                    <span class="text-xs font-semibold text-gray-700">aos {{ number_format($record->mileage, 0, ',', '.') }} km</span>
                                </div>
                                <h4 class="text-base font-bold text-gray-900 mt-1">{{ $record->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $record->description }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-base font-bold text-gray-900">R$ {{ number_format($record->total_cost, 2, ',', '.') }}</span>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    Peças: R$ {{ number_format($record->parts_cost, 2, ',', '.') }} | M.O: R$ {{ number_format($record->labor_cost, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3.5 text-xs">
                            <div>
                                <span class="text-gray-400 block text-[11px] font-medium">Oficina / Estabelecimento:</span>
                                <span class="font-medium text-gray-800">{{ $record->workshop_name ?? 'Particular' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px] font-medium">Garantia do Serviço:</span>
                                <span class="font-medium text-gray-800">
                                    {{ $record->warranty_days ? $record->warranty_days . ' dias' : 'Sem garantia' }}
                                    {{ $record->warranty_km ? 'ou ' . number_format($record->warranty_km, 0, ',', '.') . ' km' : '' }}
                                </span>
                            </div>
                            @if ($record->oil_viscosity)
                                <div>
                                    <span class="text-gray-400 block text-[11px] font-medium">Lubrificante Utilizado:</span>
                                    <span class="font-medium text-gray-800">{{ $record->oil_brand }} {{ $record->oil_viscosity }} ({{ $record->oil_liters }} L)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white border border-[#E5E7EB] rounded-2xl text-gray-400 text-xs">
                        Nenhuma manutenção registrada para este veículo até o momento.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 3: Abastecimentos -->
        <div x-show="activeTab === 'fuels'" style="display: none;" class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <span class="text-sm font-bold text-gray-900">Histórico de Abastecimentos</span>
                <a href="{{ route('fuels.index', ['vehicle_id' => $vehicle->id]) }}" 
                   class="px-3 py-1.5 bg-[#E11D48] text-white font-medium text-xs rounded-lg shadow-sm">
                    + Abastecer
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-gray-50 text-[11px] text-gray-500 font-semibold border-b border-gray-100">
                        <tr>
                            <th class="py-2.5 px-3">Data</th>
                            <th class="py-2.5 px-3">Odômetro</th>
                            <th class="py-2.5 px-3">Combustível</th>
                            <th class="py-2.5 px-3">Litros</th>
                            <th class="py-2.5 px-3">Preço/L</th>
                            <th class="py-2.5 px-3">Total Pago</th>
                            <th class="py-2.5 px-3 text-center">Consumo (Km/L)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($vehicle->fuelRecords as $fuel)
                            <tr>
                                <td class="py-2.5 px-3 font-semibold text-gray-900">{{ date('d/m/Y', strtotime($fuel->date)) }}</td>
                                <td class="py-2.5 px-3">{{ number_format($fuel->mileage, 0, ',', '.') }} km</td>
                                <td class="py-2.5 px-3">{{ $fuel->fuel_label }}</td>
                                <td class="py-2.5 px-3">{{ number_format($fuel->liters, 2, ',', '.') }} L</td>
                                <td class="py-2.5 px-3">R$ {{ number_format($fuel->price_per_liter, 2, ',', '.') }}</td>
                                <td class="py-2.5 px-3 font-semibold text-gray-900">R$ {{ number_format($fuel->total_cost, 2, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-emerald-600">
                                    {{ $fuel->km_per_liter ? number_format($fuel->km_per_liter, 1, ',', '.') . ' km/l' : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-400">Nenhum abastecimento cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 4: Obrigações / IPVA -->
        <div x-show="activeTab === 'obligations'" style="display: none;" class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <span class="text-sm font-bold text-gray-900">IPVA, Licenciamento & Seguros</span>
                <a href="{{ route('obligations.index', ['vehicle_id' => $vehicle->id]) }}" 
                   class="px-3 py-1.5 bg-[#E11D48] text-white font-medium text-xs rounded-lg shadow-sm">
                    + Nova Taxa
                </a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($vehicle->obligations as $ob)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <span class="font-semibold text-xs text-gray-900 block">{{ $ob->title }}</span>
                            <span class="text-[11px] text-gray-400">Vencimento: {{ date('d/m/Y', strtotime($ob->due_date)) }} • Exercício {{ $ob->due_year }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-xs text-gray-900 block">R$ {{ number_format($ob->final_amount, 2, ',', '.') }}</span>
                            @if ($ob->status !== 'paid')
                                <form method="POST" action="{{ route('obligations.pay', $ob) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold text-emerald-600 hover:underline">
                                        Marcar como Pago
                                    </button>
                                </form>
                            @else
                                <span class="text-[11px] text-emerald-600 font-medium">Pago</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-xs">
                        Nenhum imposto cadastrado.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 5: Odômetro -->
        <div x-show="activeTab === 'odometer'" style="display: none;" class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">
                Atualização de Odômetro
            </h3>

            <form method="POST" action="{{ route('vehicles.odometer.update', $vehicle) }}" 
                  class="p-4 bg-gray-50 rounded-xl border border-gray-200 mb-6 flex flex-col sm:flex-row items-end gap-3">
                @csrf
                <div class="flex-1 w-full">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nova Quilometragem</label>
                    <input type="number" name="current_mileage" min="{{ $vehicle->current_mileage }}" value="{{ $vehicle->current_mileage }}" required
                           class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3 py-2 text-xs font-bold text-gray-900 focus:outline-none focus:border-[#E11D48]">
                </div>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-medium text-xs rounded-lg shadow-sm transition-colors">
                    Gravar Leitura
                </button>
            </form>

            <span class="text-xs font-semibold text-gray-500 block mb-2">Histórico de Leituras:</span>
            <div class="divide-y divide-gray-100 text-xs">
                @foreach ($vehicle->odometerLogs as $log)
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="font-bold text-gray-900">{{ number_format($log->mileage, 0, ',', '.') }} km</span>
                        <span class="text-gray-400">{{ date('d/m/Y', strtotime($log->recorded_at)) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</x-app-layout>
