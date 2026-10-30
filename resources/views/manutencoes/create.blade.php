<x-app-layout>

    <!-- Header Navigation -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 sm:p-6 mb-6 shadow-sm">
        <div class="flex items-center gap-3.5">
            <a href="{{ route('maintenances.index') }}" 
               title="Voltar ao Histórico"
               class="p-2 border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 rounded-lg shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    Registrar Manutenção / Troca
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Alimente o prontuário do veículo, atualize o odômetro e recalcule os prazos de revisão.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('maintenances.store') }}" enctype="multipart/form-data" class="space-y-6" 
              x-data="{ 
                  maintType: '{{ $preselectedType }}', 
                  partsCost: 0, 
                  laborCost: 0,
                  serviceTitle: '{{ old('title', request('item_name', '')) }}',
                  setService(title, category) {
                      this.serviceTitle = title;
                      let select = document.querySelector('select[name=system_category]');
                      if (select && category) select.value = category;
                  }
              }">
            @csrf

            <!-- Identificação do Veículo & Tipo -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
                <h2 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                    Veículo & Tipo de Serviço
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Veículo <span class="text-[#E11D48]">*</span>
                        </label>
                        <select name="vehicle_id" required
                                class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                            @foreach ($vehicles as $v)
                                <option value="{{ $v->id }}" {{ $selectedVehicleId == $v->id ? 'selected' : '' }}>
                                    {{ $v->type === 'car' ? '🚗' : '🏍️' }} {{ $v->nickname }} ({{ $v->brand }} {{ $v->model }} - {{ $v->license_plate ?? 'Sem Placa' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Vincular a Item do Plano Preventivo
                        </label>
                        <select name="schedule_id"
                                class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                            <option value="">Nenhum (Manutenção avulsa / reparo corretivo)</option>
                            @foreach ($vehicles->firstWhere('id', $selectedVehicleId)?->schedules ?? [] as $sched)
                                <option value="{{ $sched->id }}" {{ $selectedScheduleId == $sched->id ? 'selected' : '' }}>
                                    {{ $sched->item_name }} (A cada {{ number_format($sched->interval_km, 0, ',', '.') }} km)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tipo de Intervenção (Cards Radio) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                        Classificação da Manutenção <span class="text-[#E11D48]">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="cursor-pointer select-none">
                            <input type="radio" name="type" value="oil_change" class="sr-only peer" x-model="maintType">
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl peer-checked:bg-rose-50/50 peer-checked:border-[#E11D48] text-center transition-all">
                                <span class="text-2xl block mb-1">🛢️</span>
                                <span class="text-xs font-bold text-gray-900 block">Troca de Óleo & Filtros</span>
                                <span class="text-[11px] text-gray-500 mt-0.5 block">Motor e lubrificação</span>
                            </div>
                        </label>

                        <label class="cursor-pointer select-none">
                            <input type="radio" name="type" value="preventive" class="sr-only peer" x-model="maintType">
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl peer-checked:bg-rose-50/50 peer-checked:border-[#E11D48] text-center transition-all">
                                <span class="text-2xl block mb-1">🛡️</span>
                                <span class="text-xs font-bold text-gray-900 block">Preventiva Programada</span>
                                <span class="text-[11px] text-gray-500 mt-0.5 block">Revisão por quilometragem</span>
                            </div>
                        </label>

                        <label class="cursor-pointer select-none">
                            <input type="radio" name="type" value="corrective" class="sr-only peer" x-model="maintType">
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl peer-checked:bg-rose-50/50 peer-checked:border-[#E11D48] text-center transition-all">
                                <span class="text-2xl block mb-1">🔧</span>
                                <span class="text-xs font-bold text-gray-900 block">Corretiva / Reparo</span>
                                <span class="text-[11px] text-gray-500 mt-0.5 block">Reparo inesperado</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Dados da Intervenção -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
                <h2 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                    Detalhes do Serviço Realizado
                </h2>

                <!-- Chips Rápidos de Seleção de Serviços Automotivos -->
                <div>
                    <span class="text-xs text-gray-500 font-medium block mb-2">
                        Preenchimento Rápido (1 Clique):
                    </span>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" @click="setService('Troca de Óleo 5W30 + Filtro', 'engine_oil')" 
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                            🛢️ Óleo + Filtro
                        </button>
                        <button type="button" @click="setService('Substituição de Pastilhas de Freio', 'brakes')" 
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                            🛑 Pastilhas de Freio
                        </button>
                        <button type="button" @click="setService('Revisão de Suspensão & Amortecedores', 'suspension_steering')" 
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                            📐 Suspensão & Pivôs
                        </button>
                        <button type="button" @click="setService('Troca de Óleo da Transmissão', 'transmission')" 
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                            ⚙️ Câmbio / Diferencial
                        </button>
                        <button type="button" @click="setService('Troca do Fluido de Arrefecimento', 'cooling_climate')" 
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                            ❄️ Arrefecimento
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Título da Manutenção <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="text" name="title" x-model="serviceTitle" placeholder="Ex: Revisão 40.000 km, Troca de Pastilhas" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Subsistema Mecânico <span class="text-[#E11D48]">*</span>
                        </label>
                        <select name="system_category" required
                                class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                            @foreach ($categories as $catKey => $catLabel)
                                <option value="{{ $catKey }}">{{ $catLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Data de Execução <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="date" name="performed_at" value="{{ old('performed_at', date('Y-m-d')) }}" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Quilometragem (Odômetro) <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="number" name="mileage" value="{{ old('mileage', $vehicles->firstWhere('id', $selectedVehicleId)?->current_mileage) }}" min="0" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Oficina / Mecânico Responsável
                        </label>
                        <input type="text" name="workshop_name" value="{{ old('workshop_name') }}" placeholder="Ex: Autocenter Brasil, Concessionária"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>
                </div>

                <!-- Painel Específico para Troca de Óleo -->
                <div x-show="maintType === 'oil_change'" class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                    <span class="text-xs font-bold text-gray-800 uppercase block pb-1 border-b border-gray-200">
                        Especificações de Óleo & Filtros
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Viscosidade / Norma</label>
                            <input type="text" name="oil_viscosity" placeholder="Ex: 5W30 Sintético API SP"
                                   class="w-full px-3 py-2 bg-white border border-[#D1D5DB] rounded-lg text-xs">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Marca do Óleo</label>
                            <input type="text" name="oil_brand" placeholder="Ex: Motul, Castrol, Mobil"
                                   class="w-full px-3 py-2 bg-white border border-[#D1D5DB] rounded-lg text-xs">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Volume em Litros</label>
                            <input type="number" step="0.1" name="oil_liters" placeholder="Ex: 3.5"
                                   class="w-full px-3 py-2 bg-white border border-[#D1D5DB] rounded-lg text-xs">
                        </div>
                    </div>

                    <div class="pt-2">
                        <span class="block text-xs font-medium text-gray-700 mb-1.5">Filtros Trocados:</span>
                        <div class="flex flex-wrap gap-4 text-xs">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filters_replaced[]" value="oil" checked class="w-4 h-4 rounded border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20">
                                <span class="text-gray-700">Filtro de Óleo</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filters_replaced[]" value="air" class="w-4 h-4 rounded border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20">
                                <span class="text-gray-700">Filtro de Ar</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filters_replaced[]" value="fuel" class="w-4 h-4 rounded border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20">
                                <span class="text-gray-700">Filtro de Combustível</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filters_replaced[]" value="cabin" class="w-4 h-4 rounded border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20">
                                <span class="text-gray-700">Filtro de Cabine (A/C)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Diagnóstico / Observações do Mecânico</label>
                    <textarea name="description" rows="2.5" placeholder="Observações do mecânico sobre o estado das peças, testes realizados..."
                              class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15"></textarea>
                </div>
            </div>

            <!-- Custos & Garantia -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
                <h2 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                    Custos & Garantia
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Valor em Peças (R$)</label>
                        <input type="number" step="0.01" name="parts_cost" x-model.number="partsCost" placeholder="0,00"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Mão de Obra (R$)</label>
                        <input type="number" step="0.01" name="labor_cost" x-model.number="laborCost" placeholder="0,00"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Custo Total Consolidado</label>
                        <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-gray-900">
                            R$ <span x-text="((partsCost || 0) + (laborCost || 0)).toFixed(2).replace('.', ',')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Garantia (Dias)</label>
                        <input type="number" name="warranty_days" placeholder="Ex: 90"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Garantia (KM)</label>
                        <input type="number" name="warranty_km" placeholder="Ex: 5000"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Comprovante / Nota Fiscal</label>
                        <input type="file" name="receipt_photo" accept="image/*"
                               class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('maintenances.index') }}" 
                   class="px-5 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium text-sm rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-semibold text-sm rounded-lg shadow-sm transition-all">
                    Gravar Manutenção &rarr;
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
