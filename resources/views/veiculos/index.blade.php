<x-app-layout>

    <!-- Top Header Bar -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 sm:p-6 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    Minha Garagem (Veículos)
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Gerencie a frota, especificações de catálogo, odômetro e planos preventivos.
                </p>
            </div>

            <a href="{{ route('vehicles.create') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-semibold rounded-lg shadow-sm transition-all self-start sm:self-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Cadastrar Veículo</span>
            </a>
        </div>
    </div>

    @if ($vehicles->isEmpty())
        <div class="p-12 text-center bg-white border border-[#E5E7EB] rounded-2xl shadow-sm max-w-lg mx-auto my-8">
            <div class="w-14 h-14 bg-rose-50 text-[#E11D48] rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 border border-rose-100">
                🚗
            </div>
            <h3 class="text-base font-bold text-gray-900">Nenhum Veículo Cadastrado</h3>
            <p class="text-xs text-gray-500 mt-1 mb-6 leading-relaxed">
                Adicione seu primeiro automóvel ou motocicleta para ativar o cronograma inteligente de revisões preventivas.
            </p>
            <a href="{{ route('vehicles.create') }}" 
               class="px-5 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-semibold rounded-lg shadow-sm inline-block transition-colors">
                Cadastrar Veículo Agora &rarr;
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($vehicles as $vehicle)
                <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden">
                    <div>
                        <!-- Header do Card -->
                        <div class="p-5 border-b border-gray-100 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-xl shrink-0">
                                    {{ $vehicle->type === 'car' ? '🚗' : '🏍️' }}
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-gray-900 leading-tight">
                                        {{ $vehicle->nickname }}
                                    </h2>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $vehicle->brand }} {{ $vehicle->model }} • {{ $vehicle->year_manufacture }}/{{ $vehicle->year_model }}
                                    </p>
                                </div>
                            </div>

                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $vehicle->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600' }}">
                                {{ $vehicle->status === 'active' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>

                        <!-- Métricas e Odômetro -->
                        <div class="p-5 space-y-4 text-xs">
                            
                            <!-- Placa & Severidade -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="text-[10px] text-gray-400 font-semibold uppercase block">Placa:</span>
                                    <span class="font-bold text-sm text-gray-900">{{ $vehicle->license_plate ?? 'Sem Placa' }}</span>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="text-[10px] text-gray-400 font-semibold uppercase block">Uso:</span>
                                    <span class="font-semibold text-xs text-gray-800">
                                        {{ $vehicle->usage_severity === 'severe' ? 'Severo (Urbano)' : 'Normal (Estrada)' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Odômetro Atual -->
                            <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-semibold text-gray-400 uppercase">Odômetro Registrado:</span>
                                    <span class="text-xs font-semibold text-gray-600">{{ $vehicle->average_daily_km }} km/dia</span>
                                </div>
                                <div class="text-xl font-bold text-gray-900 mt-1">
                                    {{ number_format($vehicle->current_mileage, 0, ',', '.') }} km
                                </div>
                            </div>

                            <!-- Alertas de Revisão -->
                            @php
                                $vOverdue = $vehicle->schedules->where('status', 'overdue')->count();
                                $vWarning = $vehicle->schedules->where('status', 'warning')->count();
                            @endphp
                            <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                                <span class="text-gray-500">Status do Plano:</span>
                                <div class="flex items-center gap-1.5">
                                    @if ($vOverdue > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-[#E11D48] border border-rose-200">
                                            {{ $vOverdue }} vencido(s)
                                        </span>
                                    @elseif ($vWarning > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ $vWarning }} em atenção
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            100% em dia
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Rodapé do Card com Ações -->
                    <div class="p-4 bg-gray-50/75 border-t border-gray-100 flex items-center justify-between gap-2">
                        <a href="{{ route('vehicles.show', $vehicle->id) }}" 
                           class="flex-1 py-2 bg-white hover:bg-gray-100 text-gray-800 text-center font-medium text-xs rounded-lg border border-gray-200 transition-colors shadow-sm">
                            Ver Prontuário &rarr;
                        </a>

                        <a href="{{ route('vehicles.edit', $vehicle->id) }}" 
                           class="px-3.5 py-2 text-gray-500 hover:text-gray-800 text-xs font-medium rounded-lg hover:bg-gray-100 transition-colors">
                            Editar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-app-layout>
