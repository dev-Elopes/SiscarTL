<x-app-layout>

    <!-- Top Navigation Header -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 sm:p-6 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    Histórico de Manutenções
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Registro de todas as trocas de óleo, revisões programadas e reparos executados.
                </p>
            </div>

            <a href="{{ route('maintenances.create') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-semibold rounded-lg shadow-sm transition-all self-start sm:self-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Registrar Nova Manutenção</span>
            </a>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 sm:p-5 mb-6 shadow-sm text-xs">
        <form method="GET" action="{{ route('maintenances.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Veículo</label>
                <select name="vehicle_id" class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48]">
                    <option value="">Todos os Veículos</option>
                    @foreach ($vehicles as $v)
                        <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->nickname }} ({{ $v->license_plate ?? 'Sem Placa' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Tipo de Serviço</label>
                <select name="type" class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48]">
                    <option value="">Todos os Tipos</option>
                    <option value="oil_change" {{ request('type') == 'oil_change' ? 'selected' : '' }}>Troca de Óleo & Filtros</option>
                    <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Preventiva Programada</option>
                    <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Corretiva / Reparo</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Subsistema</label>
                <select name="system_category" class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48]">
                    <option value="">Todos os Subsistemas</option>
                    @foreach ($categories as $catKey => $catLabel)
                        <option value="{{ $catKey }}" {{ request('system_category') == $catKey ? 'selected' : '' }}>{{ $catLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-gray-900 hover:bg-gray-800 text-white font-medium text-xs rounded-lg transition-colors shadow-sm">
                    Filtrar
                </button>
                <a href="{{ route('maintenances.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs rounded-lg transition-colors">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabela Geral de Histórico -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-50/75 border-b border-gray-200 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Data & Odômetro</th>
                        <th class="py-3 px-4">Veículo</th>
                        <th class="py-3 px-4">Tipo & Categoria</th>
                        <th class="py-3 px-4">Título do Serviço</th>
                        <th class="py-3 px-4">Oficina / Mecânico</th>
                        <th class="py-3 px-4 text-right">Valor Total</th>
                        <th class="py-3 px-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($records as $record)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-gray-900">{{ date('d/m/Y', strtotime($record->performed_at)) }}</span>
                                <span class="block text-[11px] text-gray-400 font-medium">{{ number_format($record->mileage, 0, ',', '.') }} km</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">{{ $record->vehicle->nickname }}</span>
                                <span class="block text-[11px] text-gray-400">{{ $record->vehicle->license_plate ?? 'Sem Placa' }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                    {{ $record->type_label }}
                                </span>
                                <span class="block text-[11px] text-gray-400 mt-0.5">{{ $record->category_label }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-gray-900 block">{{ $record->title }}</span>
                                <span class="text-[11px] text-gray-400 line-clamp-1">{{ $record->description }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-500">
                                {{ $record->workshop_name ?? 'Particular' }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-right font-bold text-gray-900">
                                R$ {{ number_format($record->total_cost, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                <a href="{{ route('maintenances.show', $record) }}" 
                                   class="text-xs font-semibold text-[#E11D48] hover:underline">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                Nenhuma manutenção encontrada com os filtros selecionados.
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

</x-app-layout>
