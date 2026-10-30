<x-app-layout>

    <!-- Header Navigation -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 sm:p-6 mb-6 shadow-sm">
        <div class="flex items-center gap-3.5">
            <a href="{{ route('vehicles.index') }}" 
               title="Voltar à Garagem"
               class="p-2 border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 rounded-lg shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    Cadastrar Novo Veículo
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Informe os dados do veículo para gerar o cronograma de manutenção preventiva.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('vehicles.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Card 1: Identificação Básica & Tipo -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
                <h2 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                    Classificação & Dados do Veículo
                </h2>

                <!-- Seletor de Tipo (Carro vs Moto) -->
                <div class="grid grid-cols-2 gap-4">
                    <label class="cursor-pointer select-none">
                        <input type="radio" name="type" value="car" class="sr-only peer" checked>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl peer-checked:bg-rose-50 peer-checked:border-[#E11D48] text-center transition-all">
                            <span class="text-3xl block mb-1">🚗</span>
                            <span class="text-sm font-semibold text-gray-900 block">Automóvel (Carro)</span>
                            <span class="text-xs text-gray-500 mt-0.5 block">Hatch, Sedan, SUV, Picape</span>
                        </div>
                    </label>

                    <label class="cursor-pointer select-none">
                        <input type="radio" name="type" value="motorcycle" class="sr-only peer">
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl peer-checked:bg-rose-50 peer-checked:border-[#E11D48] text-center transition-all">
                            <span class="text-3xl block mb-1">🏍️</span>
                            <span class="text-sm font-semibold text-gray-900 block">Motocicleta</span>
                            <span class="text-xs text-gray-500 mt-0.5 block">Street, Trail, Scooter</span>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Apelido do Veículo <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="text" name="nickname" value="{{ old('nickname') }}" placeholder="Ex: Onix Plus Turbo" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Marca <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="text" list="brands_list" name="brand" value="{{ old('brand') }}" placeholder="Ex: Chevrolet, Fiat, Honda" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                        <datalist id="brands_list">
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Modelo <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="text" name="model" value="{{ old('model') }}" placeholder="Ex: Onix 1.0 Turbo" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Ano Fabricação <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="number" name="year_manufacture" value="{{ old('year_manufacture', date('Y') - 2) }}" min="1950" max="{{ date('Y') + 1 }}" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Ano Modelo <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="number" name="year_model" value="{{ old('year_model', date('Y') - 1) }}" min="1950" max="{{ date('Y') + 2 }}" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Combustível <span class="text-[#E11D48]">*</span>
                        </label>
                        <select name="fuel_type" required
                                class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                            <option value="flex" {{ old('fuel_type') == 'flex' ? 'selected' : '' }}>Flex (Gasolina / Etanol)</option>
                            <option value="gasoline" {{ old('fuel_type') == 'gasoline' ? 'selected' : '' }}>Gasolina</option>
                            <option value="ethanol" {{ old('fuel_type') == 'ethanol' ? 'selected' : '' }}>Etanol</option>
                            <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="cng" {{ old('fuel_type') == 'cng' ? 'selected' : '' }}>GNV</option>
                            <option value="hybrid" {{ old('fuel_type') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                            <option value="electric" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>Elétrico</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card 2: Odômetro & Condição de Uso -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
                <h2 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                    Odômetro & Regime de Uso
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Quilometragem Atual (km) <span class="text-[#E11D48]">*</span>
                        </label>
                        <input type="number" name="current_mileage" value="{{ old('current_mileage', 0) }}" min="0" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Média Estimada (km / mês)
                        </label>
                        <input type="number" name="average_monthly_mileage" value="{{ old('average_monthly_mileage', 1000) }}" min="100" max="20000"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Consumo Médio Estimado (km / L)
                        </label>
                        <input type="number" step="0.1" name="expected_km_per_liter" value="{{ old('expected_km_per_liter') }}" placeholder="Ex: 12.5" min="1" max="99"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                        <span class="text-[11px] text-gray-400 mt-1 block">Usado para projeção de gastos e autonomia em abastecimentos</span>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Capacidade do Tanque (Litros)
                        </label>
                        <input type="number" step="0.5" name="tank_capacity" value="{{ old('tank_capacity') }}" placeholder="Ex: 44 ou 54" min="1" max="500"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15">
                        <span class="text-[11px] text-gray-400 mt-1 block">Opcional para cálculo de autonomia por tanque</span>
                    </div>
                </div>

                <!-- Seletor de Condição de Uso -->
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                    <span class="text-xs font-bold text-gray-800 block">Condição de Rodagem:</span>
                    
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="radio" name="usage_condition" id="cond_normal" value="normal" checked
                               class="w-4 h-4 rounded-full border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20 mt-0.5">
                        <div class="text-xs">
                            <span class="font-semibold text-gray-900 block">Uso Normal (Padrão de Fábrica)</span>
                            <span class="text-gray-500 block mt-0.5">Estradas livres e viagens rodoviárias. Intervalos padrão (ex.: óleo a cada 10.000 km).</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer pt-2 border-t border-gray-200">
                        <input type="radio" name="usage_condition" id="cond_severe" value="severe"
                               class="w-4 h-4 rounded-full border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20 mt-0.5">
                        <div class="text-xs">
                            <span class="font-semibold text-[#E11D48] block">Uso Severo (Centros Urbanos / Anda e Para)</span>
                            <span class="text-gray-500 block mt-0.5">Trânsito pesado, poeira ou trajetos curtos. Reduz os prazos de óleo e filtros pela metade para preservação do motor.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Card 3: Documentação Opcional -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
                <h2 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                    Dados Complementares (Opcional)
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Placa do Veículo</label>
                        <input type="text" name="license_plate" value="{{ old('license_plate') }}" placeholder="ABC-1234 ou BRA2E19"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 uppercase focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Cor</label>
                        <input type="text" name="color" value="{{ old('color') }}" placeholder="Ex: Prata, Preto"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Tipo de Câmbio</label>
                        <select name="transmission_type"
                                class="w-full px-3.5 py-2.5 bg-white border border-[#D1D5DB] rounded-lg text-sm text-gray-900 focus:outline-none focus:border-[#E11D48]">
                            <option value="">Não informado</option>
                            <option value="manual">Manual</option>
                            <option value="automatic">Automático</option>
                            <option value="cvt">CVT</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('vehicles.index') }}" 
                   class="px-5 py-2.5 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium text-sm rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-semibold text-sm rounded-lg shadow-sm transition-all">
                    Salvar Veículo & Gerar Plano
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
