<x-app-layout>

    <!-- Header Navigation -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 sm:p-6 mb-6 shadow-sm">
        <div class="flex items-center gap-3.5">
            <a href="{{ route('vehicles.show', $vehicle) }}" 
               title="Voltar ao Prontuário"
               class="p-2 border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 rounded-lg shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    Editar Veículo: {{ $vehicle->nickname }}
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $vehicle->brand }} {{ $vehicle->model }} • Placa: {{ $vehicle->license_plate ?? 'Sem Placa' }}
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
                <h2 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                    Dados Cadastrais & Operacionais
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Apelido do Veículo</label>
                        <input type="text" name="nickname" value="{{ old('nickname', $vehicle->nickname) }}" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Placa</label>
                        <input type="text" name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 uppercase focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Situação na Garagem</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                            <option value="active" {{ $vehicle->status === 'active' ? 'selected' : '' }}>Ativo na Garagem</option>
                            <option value="sold" {{ $vehicle->status === 'sold' ? 'selected' : '' }}>Vendido (Congelar Alertas)</option>
                            <option value="inactive" {{ $vehicle->status === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Média Mensal (km/mês)</label>
                        <input type="number" name="average_monthly_mileage" value="{{ old('average_monthly_mileage', $vehicle->average_monthly_mileage) }}"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Consumo Médio (km / L)</label>
                        <input type="number" step="0.1" name="expected_km_per_liter" value="{{ old('expected_km_per_liter', $vehicle->expected_km_per_liter) }}" placeholder="Ex: 12.5" min="1" max="99"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Capacidade do Tanque (L)</label>
                        <input type="number" step="0.5" name="tank_capacity" value="{{ old('tank_capacity', $vehicle->tank_capacity) }}" placeholder="Ex: 44 ou 54" min="1" max="500"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Cor</label>
                        <input type="text" name="color" value="{{ old('color', $vehicle->color) }}"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Condição de Uso</label>
                        <select name="usage_condition" class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                            <option value="normal" {{ $vehicle->usage_condition === 'normal' ? 'selected' : '' }}>Uso Normal (Padrão)</option>
                            <option value="severe" {{ $vehicle->usage_condition === 'severe' ? 'selected' : '' }}>Uso Severo (Reduz Prazos)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Observações Adicionais</label>
                    <textarea name="notes" rows="2" class="w-full px-3.5 py-2 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">{{ old('notes', $vehicle->notes) }}</textarea>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('vehicles.show', $vehicle) }}" 
                   class="px-5 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium text-sm rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-semibold text-sm rounded-lg shadow-sm transition-all">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
