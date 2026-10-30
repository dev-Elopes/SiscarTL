<x-app-layout>

    <!-- Top Navigation Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#E11D48]"></span>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    Relatórios, Exportações & Prontuário de Venda
                </h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Emissão de Laudo Técnico impresso (A4) para comprovação de procedência mecânica e exportação de dados para planilhas
            </p>
        </div>
    </div>

    @if ($vehicles->isEmpty())
        <div class="p-12 text-center bg-white border border-gray-200 rounded-2xl shadow-sm">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <h3 class="text-sm font-semibold text-gray-900">Nenhum veículo disponível</h3>
            <p class="text-xs text-gray-500 mt-1">Cadastre um veículo para emitir laudos e relatórios.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($vehicles as $v)
                <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-sm hover:border-gray-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-700 shrink-0">
                            @if ($v->type === 'car')
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM3 13l2-7h14l2 7M3 13h18M3 13v5a1 1 0 001 1h1m16-6v5a1 1 0 01-1 1h-1"/></svg>
                            @else
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h3 class="text-base font-bold text-gray-900">{{ $v->nickname }}</h3>
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-md">
                                    {{ $v->license_plate ?? 'SEM PLACA' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $v->brand }} {{ $v->model }} ({{ $v->year_manufacture }}/{{ $v->year_model }}) • Odômetro atual: <strong class="text-gray-900 font-semibold">{{ number_format($v->current_mileage, 0, ',', '.') }} KM</strong>
                            </p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                <span><strong class="text-gray-800 font-medium">{{ $v->maintenanceRecords->count() }}</strong> revisões no prontuário</span>
                                <span>•</span>
                                <span>Investimento histórico: <strong class="text-gray-900 font-semibold">R$ {{ number_format($v->maintenanceRecords->sum('total_cost'), 2, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <!-- Exportar CSV -->
                        <a href="{{ route('reports.export-csv', $v) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl shadow-sm transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Exportar CSV</span>
                        </a>

                        <!-- Laudo para Venda (PDF / Impressão) -->
                        <a href="{{ route('reports.certificate', $v) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-4 sm:px-5 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Emitir Laudo A4</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-app-layout>
