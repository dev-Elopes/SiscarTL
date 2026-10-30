<x-app-layout>

    <!-- Header Navigation & Breadcrumb -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('maintenances.index') }}" 
               title="Voltar ao Histórico"
               class="p-2.5 bg-white border border-gray-200 hover:border-gray-300 text-gray-600 hover:text-gray-900 rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#E11D48]"></span>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                        Prontuário da Intervenção #{{ str_pad($maintenance->id, 4, '0', STR_PAD_LEFT) }}
                    </h1>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">
                    Veículo: <span class="font-medium text-gray-800">{{ $vehicle->nickname }}</span> • Realizada em {{ date('d/m/Y', strtotime($maintenance->performed_at)) }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19l-7-7 7-7"/></svg>
                <span>Prontuário do Veículo</span>
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Main Card with subtle top accent line -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1 bg-[#E11D48] w-full"></div>

            <div class="p-6 sm:p-8 space-y-6">
                <!-- Header with Title & Cost -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-gray-100">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $maintenance->type === 'preventive' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                                {{ $maintenance->type_label }}
                            </span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs font-medium text-gray-600">Subsistema: <strong class="text-gray-900 font-semibold">{{ $maintenance->category_label }}</strong></span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $maintenance->title }}</h2>
                    </div>

                    <div class="sm:text-right bg-gray-50 sm:bg-transparent p-4 sm:p-0 rounded-xl sm:rounded-none border sm:border-0 border-gray-100">
                        <span class="text-xs text-gray-500 font-medium block">Investimento Total</span>
                        <span class="text-3xl font-extrabold text-gray-900 tracking-tight block mt-0.5">
                            R$ {{ number_format($maintenance->total_cost, 2, ',', '.') }}
                        </span>
                        <div class="flex items-center sm:justify-end gap-2 text-xs text-gray-500 mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md">Peças: R$ {{ number_format($maintenance->parts_cost, 2, ',', '.') }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md">Mão de Obra: R$ {{ number_format($maintenance->labor_cost, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Technical Details Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-4 border-b border-gray-100">
                    <div class="space-y-1">
                        <span class="text-xs text-gray-400 font-medium block">Data de Execução</span>
                        <span class="text-sm font-semibold text-gray-900">{{ date('d/m/Y', strtotime($maintenance->performed_at)) }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs text-gray-400 font-medium block">Odômetro Registrado</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($maintenance->mileage, 0, ',', '.') }} KM</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs text-gray-400 font-medium block">Oficina / Estabelecimento</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $maintenance->workshop_name ?? 'Particular / Próprio' }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs text-gray-400 font-medium block">Garantia Concedida</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $maintenance->warranty_days ? $maintenance->warranty_days . ' dias' : 'Sem garantia formal' }}
                        </span>
                    </div>
                </div>

                @if ($maintenance->description)
                    <div class="space-y-2 py-2 border-b border-gray-100">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Diagnóstico do Mecânico / Observações Técnicas</span>
                        <div class="bg-gray-50 border border-gray-200/70 rounded-xl p-4 text-sm text-gray-700 leading-relaxed">
                            {{ $maintenance->description }}
                        </div>
                    </div>
                @endif

                <!-- Parts Breakdown Table -->
                @if ($maintenance->items->isNotEmpty())
                    <div class="space-y-3 pt-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Discriminação de Peças e Componentes</span>
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase">
                                    <tr>
                                        <th class="py-3 px-4">Item / Peça</th>
                                        <th class="py-3 px-4 text-center">Quantidade</th>
                                        <th class="py-3 px-4 text-right">Valor Unitário</th>
                                        <th class="py-3 px-4 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    @foreach ($maintenance->items as $item)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="py-3 px-4 font-medium text-gray-900">{{ $item->item_name }}</td>
                                            <td class="py-3 px-4 text-center text-gray-600">{{ $item->quantity }}</td>
                                            <td class="py-3 px-4 text-right text-gray-600">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                            <td class="py-3 px-4 text-right font-semibold text-gray-900">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Footer Action Buttons -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                        &larr; Voltar ao Prontuário do Veículo
                    </a>

                    <form method="POST" action="{{ route('maintenances.destroy', $maintenance) }}" onsubmit="return confirm('Deseja realmente excluir este registro de manutenção?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3.5 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 border border-rose-200 rounded-xl transition-colors">
                            Excluir Registro
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
